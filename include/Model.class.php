<?php

include_once(dirname(__FILE__) . "/../global.php");
class Model{
    protected $dbhandle;
    public function __construct(){
        $this->dbhandle = new Database();
    }

    function getQrcode($path, $width){
        $appId = "wx04a548f574c826fb";
        $appSecret = "68f1ef93cc6c9153e8d49b95899aa9a2";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";
        $resData = file_get_contents($url);
        $resArr = json_decode($resData, true);
        $accessToken = $resArr['access_token'];
        if ($accessToken){
            $url = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=$accessToken";
            $data = json_encode(array('path'=>$path, 'width'=>$width));
            $opts = array(
                'http'=> array(
                    'method'=>'POST',
                    'header'=>"Content-type: application/x-www-form-urlencoded",
                    'content'=>$data
                )
            );
            $context = stream_context_create($opts);
            $resData = file_get_contents($url, false, $context);
            $time = time();
            file_put_contents("/home/wwwroot/default/freeman/data/$time.png", $resData);
            return array('name'=> "$time.png");
        }
    }

    function login($code, $raw_data, $iv){
        $appId = "wx04a548f574c826fb";
        $appSecret = "68f1ef93cc6c9153e8d49b95899aa9a2";
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appId&secret=$appSecret&js_code=$code&grant_type=authorization_code";
        $resJson = file_get_contents($url);
        $resArr = json_decode($resJson, true);
        if (array_key_exists('errcode', $resArr)) {
            printf ("login failed: %s\n", $resArr['errmsg']);
            exit();
        }
        $pc = new WXBizDataCrypt($appId, $resArr['session_key']);
        $errCode = $pc->decryptData($raw_data, $iv, $data);
        if ($errCode !== 0) {
            return $errCode;
        } 
        $data = json_decode($data, true);
        $avatar = $data['avatarUrl'];
        $unionId = $data['unionId'];
        $nickName = $data['nickName'];
        $sql = "SELECT uid, role, cur_proj, cur_group FROM fm_user WHERE unionid='$unionId'";
        $data = $this->dbhandle->query($sql);
        if (count($data)===0) {
            $sql = "INSERT INTO fm_user(unionid, avatar, nick_name) VALUES('$unionId', '$avatar', '$nickName')";
            $data = $this->dbhandle->execute($sql);
            $uid = $this->dbhandle->insertId();
            return array("uid"=>"$uid", "role"=>"0", "proj_id"=>"0", "group_id"=>"0");
        }
        $uid = $data[0]['uid'];
        $role = $data[0]['role'];
        $projId = $data[0]['cur_proj'];
        $group_id = $data[0]['cur_group'];
        return array("uid"=>$uid, "role"=>$role, "proj_id"=>$projId, "group_id"=>$group_id);
    }

    function changeRole($uid, $role) {
        $uid = intval($uid);
        $role = intval($role);
        $sql = "UPDATE fm_user SET role=$role WHERE uid=$uid";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function createGroup($uid, $title){
        $uid = intval($uid);
        $time = time();
        $title = $this->dbhandle->escape($title);
        $sql = "INSERT INTO fm_group(creator_id, title) VALUES($uid, '$title')";
        $data = $this->dbhandle->execute($sql);
        $group_id = $this->dbhandle->insertId();
        $sql = "UPDATE fm_user SET cur_group=$group_id WHERE uid=$uid";
        $data = $this->dbhandle->execute($sql);
        return array('group_id'=>"$group_id");
    }

    function getGroupInfo($uid, $group_id){
        $uid = intval($uid);
        $group_id = intval($group_id);
        $sql = "SELECT group_id, title FROM fm_group WHERE group_id=$group_id";
        $result = $this->dbhandle->query($sql);
        $data = $result[0];
        $sql = "SELECT proj_id, title, avatar, nick_name FROM fm_project, fm_user WHERE fm_user.uid = fm_project.creator_id AND proj_id IN (SELECT proj_id FROM fm_group_proj WHERE group_id = $group_id)";
        $projects = $this->dbhandle->query($sql);
    /*$sql = "SELECT proj_id FROM fm_group_proj WHERE group_id=$group_id";
    /*$sql = "SELECT proj_id, MIN(status) AS status FROM fm_marker WHERE uid=$uid AND proj_id IN ($sql) GROUP BY proj_id";
    $status = $this->dbhandle->query($sql);*/
        $data['projects'] = array();
        foreach($projects as $project) {
            $project['marker'] = 0;
            $result = $this->getMarker($uid, $project['proj_id']);
            if (count($result)===0) {
                $project['marker'] = 1;
            }
      /*foreach($status as $item){
        if ($item['proj_id']===$project['proj_id'] and $item['status']==='1'){
          $project['marker'] = 1;
        }
    }*/
        $data['projects'][] = $project;
        }
        return $data;
    }

    function getMyGroup($uid){
        $uid = intval($uid);
        $sql = "SELECT group_id, title FROM fm_group WHERE creator_id=$uid OR group_id IN (SELECT group_id FROM fm_group_proj WHERE proj_id IN(SELECT proj_id FROM fm_proj_member WHERE uid=$uid))";
        $data = $this->dbhandle->query($sql);
        return $data;
    }

    function updateGroupInfo($group_id, $title){
        $group_id = intval($group_id);
        $title = $this->dbhandle->escape($title);
        $sql = "UPDATE fm_group SET title='$title' WHERE group_id=$group_id";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function changeCurGroup($uid, $group_id){
        $uid = intval($uid);
        $group_id = intval($group_id);
        $sql = "UPDATE fm_user SET cur_group=$group_id WHERE uid=$uid";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function addGroupProj($group_id, $proj_id){
        $proj_id = intval($proj_id);
        $group_id = intval($group_id);
        $sql = "SELECT * FROM fm_group_proj WHERE proj_id=$proj_id AND group_id=$group_id";
        $result = $this->dbhandle->query($sql);
        if (count($result)!==0) {
            return array('errmsg'=>'exist');
        }
        $sql = "INSERT INTO fm_group_proj(proj_id, group_id) VALUES($proj_id, $group_id)";
        $data = $this->dbhandle->execute($sql);
        $sql = "UPDATE fm_user SET cur_group=$group_id WHERE uid IN (SELECT uid FROM fm_proj_member WHERE proj_id=$proj_id)";
        $this->dbhandle->execute($sql);
        return $data;
    }

    function deleteGroupProj($group_id, $proj_id){
        $group_id = intval($group_id);
        $proj_id = intval($proj_id);
        $sql = "DELETE FROM fm_group_proj WHERE group_id=$group_id AND proj_id=$proj_id";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function createProj($uid, $title, $intro, $vision){
        $uid = intval($uid);
        $title = $this->dbhandle->escape($title);
        $intro = $this->dbhandle->escape($intro);
        $vision = $this->dbhandle->escape($vision);
        $data = array();
        $sql = "INSERT INTO fm_project(creator_id, title, intro, vision) VALUES($uid, '$title', '$intro', '$vision')";
        $data[] = $this->dbhandle->execute($sql);
        $proj_id = $this->dbhandle->insertId();
        $sql = "UPDATE fm_user SET cur_proj=$proj_id WHERE uid=$uid";
        $data[] = $this->dbhandle->execute($sql);
        $sql = "INSERT INTO fm_proj_member(uid, proj_id, is_creator) VALUES($uid, $proj_id, 1)";
        $data[] = $this->dbhandle->execute($sql);
        return array("proj_id"=>"$proj_id");
    }

    function updateProjInfo($uid, $proj_id, $field, $value){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $field = $this->dbhandle->escape($field);
        $value = $this->dbhandle->escape($value);
        $sql = "UPDATE fm_project SET $field='$value' WHERE proj_id=$proj_id";
        $data = $this->dbhandle->execute($sql);
        $this->resetMarker($uid, $proj_id, 0, 0);
        return $data;
    }

    function getProjInfo($uid, $proj_id){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $sql = "SELECT * FROM fm_project WHERE proj_id=$proj_id";
        $result = $this->dbhandle->query($sql);
        $data = $result[0];
        $sql = "SELECT * FROM fm_user WHERE uid IN (SELECT uid FROM fm_proj_member WHERE proj_id=$proj_id ORDER BY is_creator DESC)";
        $data['members'] = $this->dbhandle->query($sql);
        $this->updateMarker($uid, $proj_id, 0, 0);
        return $data;
    }

    function getMyProject($uid){
        $uid = intval($uid);
        $sql = "SELECT proj_id, title FROM fm_project WHERE proj_id IN(SELECT proj_id FROM fm_proj_member WHERE uid=$uid)";
        $data = $this->dbhandle->query($sql);
        return $data;
    }

    function changeCurProj($uid, $proj_id){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $sql = "UPDATE fm_user SET cur_proj=$proj_id WHERE uid=$uid";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function addProjMember($uid, $proj_id){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $sql = "SELECT proj_id from fm_proj_member WHERE uid=$uid AND proj_id=$proj_id";
        $result = $this->dbhandle->query($sql);
        if (count($result)!==0) {
            return array('errmsg'=>'failed');
        }
        $sql = "INSERT INTO fm_proj_member(uid, proj_id) VALUES($uid, $proj_id)";
        $data = $this->dbhandle->execute($sql);
        $this->changeCurProj($uid, $proj_id);
        $sql = "SELECT group_id FROM fm_group_proj WHERE proj_id=$proj_id ORDER BY join_time DESC";
        $result = $this->dbhandle->query($sql);
        if (count($result)!==0) {
            $group_id = $result['0'];
            $this->changeCurGroup($uid, $group_id);
            $data['group_id'] = $group_id;
        }
        return $data;
    }

    function deleteProjMember($proj_id, $uid){
        $proj_id = intval($proj_id);
        $uid = intval($uid);
        $sql = "DELETE FROM fm_proj_member WHERE proj_id=$proj_id AND uid=$uid";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function getPulseList($uid, $proj_id, $pulse_id){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $pulse_id = intval($pulse_id);
        $sql = "SELECT * FROM fm_pulse WHERE proj_id=$proj_id AND pulse_id=$pulse_id";
        $result = $this->dbhandle->query($sql);
        $sql = "SELECT field_id, status FROM fm_marker WHERE uid=$uid AND proj_id=$proj_id AND field=$pulse_id";
        $status = $this->dbhandle->query($sql);
        $data = array();
        foreach($result as $record) {
            $record['marker'] = 0;
            foreach($status as $item){
                if ($item['field_id']===$record['pulse_no'] and $item['status']==='1'){
                    $record['marker'] = 1;
                }
            }
            $data[] = $record;
        }
        return $data;
    }

    function getPulseInfo($uid, $proj_id, $pulse_id, $pulse_no) {
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $pulse_id = intval($pulse_id);
        $pulse_no = intval($pulse_no);
        $sql = "SELECT * FROM fm_pulse WHERE proj_id=$proj_id AND pulse_id=$pulse_id AND pulse_no=$pulse_no";
        $result = $this->dbhandle->query($sql);
        if (!$result) {
            $data = array();
        } else {
            $data = $result[0];
        }
        $data['comments'] = $this->getComment($proj_id, $pulse_id, $pulse_no);
        $this->updateMarker($uid, $proj_id, $pulse_id, $pulse_no);
        return $data;
    }

    function updatePulseInfo($uid, $proj_id, $pulse_id, $pulse_no, $content){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $pulse_id = intval($pulse_id);
        $pulse_no = intval($pulse_no);
        $content = $this->dbhandle->escape($content);
        $sql = "UPDATE fm_pulse SET content='$content', redactor_id=$uid WHERE proj_id=$proj_id AND pulse_id=$pulse_id AND pulse_no=$pulse_no";
        $data = $this->dbhandle->execute($sql);
        if ($data===0) {
            $data = $this->addPulse($uid, $proj_id, $pulse_id, $pulse_no, $content);
        }
        $this->resetMarker($uid, $proj_id, $pulse_id, $pulse_no);
        return $data;
    }

    function addPulse($uid, $proj_id, $pulse_id, $pulse_no, $content) {
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $pulse_id = intval($pulse_id);
        $pulse_no = intval($pulse_no);
        $content = $this->dbhandle->escape($content);
        $sql = "INSERT INTO fm_pulse(proj_id, pulse_id, pulse_no, content, redactor_id) VALUES($proj_id, $pulse_id, $pulse_no, '$content', $uid)";
        $data = $this->dbhandle->execute($sql);
        $this->updateMarker($uid, $proj_id, $pulse_id, $pulse_no);
        return $data;
    }

    function getCanvasInfo($uid, $proj_id, $canvas_id){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $canvas_id = intval($canvas_id);
        $sql = "SELECT * FROM fm_canvas WHERE proj_id=$proj_id AND canvas_id=$canvas_id ORDER BY insert_id DESC";
        $result = $this->dbhandle->query($sql);
        if (!$result) {
            $data = array();
        } else {
            $data = $result[0];
        }
        $data['comments'] = $this->getComment($proj_id, $canvas_id, 0);
        $sql = "SELECT * FROM fm_card WHERE proj_id=$proj_id AND canvas_id=$canvas_id ORDER BY card_order DESC";
        $cards = $this->dbhandle->query($sql);
        $sql = "SELECT field_id, status FROM fm_marker WHERE uid=$uid AND proj_id=$proj_id AND field=$canvas_id";
        $status = $this->dbhandle->query($sql);
        $data['cards'] = array();
        foreach($cards as $card) {
            $card['marker'] = 0;
            foreach($status as $item){
                if ($item['field_id']===$card['card_id'] and $item['status']==='1'){
                    $card['marker'] = 1;
                }
            }
            $data['cards'][] = $card;
        }
        $this->updateMarker($uid, $proj_id, $canvas_id, 0);
        return $data;
    }

    function updateCanvasInfo($uid, $proj_id, $canvas_id, $content){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $canvas_id = intval($canvas_id);
        $content = $this->dbhandle->escape($content);
        $sql = "INSERT INTO fm_canvas(proj_id, canvas_id, redactor_id, content) VALUES($proj_id, $canvas_id, $uid, '$content')";
        $data = $this->dbhandle->execute($sql);
        $this->resetMarker($uid, $proj_id, $canvas_id, 0);
        return $data;
    }

    function getCanvasLog($proj_id, $canvas_id){
        $proj_id = intval($proj_id);
        $canvas_id = intval($canvas_id);
        $sql = "SELECT * FROM fm_canvas WHERE proj_id=$proj_id AND canvas_id=$canvas_id ORDER BY insert_id DESC";
        $data = $this->dbhandle->query($sql);
        return $data;
    }

    function addCard($uid, $proj_id, $canvas_id, $title, $assumption){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $canvas_id = intval($canvas_id);
        $title = $this->dbhandle->escape($title);
        $assumption = $this->dbhandle->escape($assumption);
        $sql = "SELECT card_order FROM fm_card WHERE proj_id=$proj_id ORDER BY card_order DESC";
        $result = $this->dbhandle->query($sql);
        if (count($result)!==0) {
            $max_order = $result['0']['card_order'];
        } else {
            $max_order = 0;
        }
        $order = $max_order + 1;
        $sql = "INSERT INTO fm_card(card_order, proj_id, canvas_id, title, assumption) VALUES($order, $proj_id, $canvas_id, '$title', '$assumption')";
        $data = $this->dbhandle->execute($sql);
        $card_id = $this->dbhandle->insertId();
        $this->updateMarker($uid, $proj_id, $canvas_id, $card_id);
        return $data;
    }

    function changeCardOrder ($proj_id, $card_id) {
        $proj_id = intval($proj_id);
        $card_id = intval($card_id);
        $sql = "SELECT card_order FROM fm_card WHERE proj_id=$proj_id ORDER BY card_order DESC";
        $result = $this->dbhandle->query($sql);
        if (count($result)!==0) {
            $max_order = $result['0']['card_order'];
        } else {
            $max_order = 0;
        }
        $order = $max_order + 1;
        $sql = "UPDATE fm_card SET card_order=$order WHERE card_id=$card_id";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function upwardCard ($proj_id, $card_id) {
        $proj_id = intval($proj_id);
        $card_id = intval($card_id);
        $sql = "SELECT card_order, card_id FROM fm_card WHERE proj_id=$proj_id AND card_id=$card_id";
        $result = $this->dbhandle->query($sql);
        $card_order = $result[0]['card_order'];
        $sql = "SELECT card_order, card_id FROM fm_card WHERE card_order>$card_order ORDER BY card_order";
        $result = $this->dbhandle->query($sql);
        if (!count($result)) {
            return array("errmsg"=>'failed');
        }
        $card_order2 = $result[0]['card_order'];
        $card_id2 = $result[0]['card_id'];
        $sql = "UPDATE fm_card AS A, fm_card AS B SET A.card_order=$card_order, B.card_order=$card_order2 WHERE A.card_id=$card_id2 AND B.card_id = $card_id";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function getProjCard($uid, $proj_id){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $sql = "SELECT * from fm_card WHERE proj_id=$proj_id AND status='active' ORDER BY card_order DESC";
        $cards = $this->dbhandle->query($sql);
        $sql = "SELECT field_id, status FROM fm_marker WHERE uid=$uid AND proj_id=$proj_id AND field<=8";
        $status = $this->dbhandle->query($sql);
        $data = array();
        foreach($cards as $card) {
            $card['marker'] = 0;
            foreach($status as $item){
                if ($item['field_id']===$card['card_id'] and $item['status']==='1'){
                    $card['marker'] = 1;
                }
            }
            $data[] = $card;
        }
        return $data;
    }

    function getCardInfo($uid, $card_id){
        $uid = intval($uid);
        $card_id = intval($card_id);
        $sql = "SELECT * FROM fm_card WHERE card_id=$card_id";
        $card_info = $this->dbhandle->query($sql);
        $data = $card_info[0];
        $proj_id = $data['proj_id'];
        $canvas_id = $data['canvas_id'];
        $card_id = $data['card_id'];
        $data['comments'] = $this->getComment($proj_id, $canvas_id, $card_id);
        $this->updateMarker($uid, $proj_id, $canvas_id, $card_id);
        return $data;
    }

    function getComment ($proj_id, $field, $field_id) {
        $proj_id = intval($proj_id);
        $field = intval($field);
        $field_id = intval($field_id);
        $sql = "SELECT * FROM fm_comment AS A, fm_user AS B WHERE A.proj_id=$proj_id AND A.field=$field AND A.field_id=$field_id AND A.commentor_id=B.uid";
        $comments = $this->dbhandle->query($sql);
        $sql = "SELECT A.*, B.nick_name FROM fm_reply AS A, fm_user AS B WHERE A.replier_id=B.uid AND A.comment_id IN (SELECT comment_id FROM fm_comment WHERE field=$field AND field_id=$field_id)";
        $replies = $this->dbhandle->query($sql);
        for ($index= 0; $index < count($comments); $index++) {
            $comments[$index]['replies'] = array();
            foreach($replies as $item) {
                if ($comments[$index]['comment_id']===$item['comment_id']) {
                    $comments[$index]['replies'][] = $item;
                }
            }
        }
        return $comments;
    }

    function updateCardInfo($uid, $card_id, $title, $assumption, $result, $status){
        $card_id = intval($card_id);
        $title = $this->dbhandle->escape($title);
        $assumption = $this->dbhandle->escape($assumption);
        $result = $this->dbhandle->escape($result);
        $status = $this->dbhandle->escape($status);
        $sql = "UPDATE fm_card SET ";
        if ($title){
            $sql .= "title='$title', ";
        }
        if ($assumption){
            $sql .= "assumption='$assumption', ";
        }
        if ($result){
            $sql .= "result='$result', ";
        }
        if ($status){
            $sql .= "status='$status', ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE card_id=$card_id";
        $data = $this->dbhandle->execute($sql);
        $sql = "SELECT proj_id, canvas_id FROM fm_card WHERE card_id=$card_id";
        $result = $this->dbhandle->query($sql);
        $proj_id = $result[0]['proj_id'];
        $canvas_id = $result[0]['canvas_id'];
        $this->resetMarker($uid, $proj_id, $canvas_id, $card_id);
        return $data;
    }

    function deleteCard($card_id){
        $card_id = intval($card_id);
        $sql = "DELETE FROM fm_card WHERE card_id=$card_id";
        $data = array();
        $data[] = $this->dbhandle->execute($sql);
        $sql = "SELECT comment_id FROM fm_card_comment WHERE card_id=$card_id";
        $result = $this->dbhandle->query($sql);
        if (count($result)>0) {
            $result = array_values($result);
            $sql = "DELETE FROM fm_card_reply WHERE comment_id IN $result";
            $data[] = $this->dbhandle->execute($sql);
            $sql = "DELETE FROM fm_card_comment WHERE card_id=$card_id";
            $data[] = $this->dbhandle->execute($sql);
        }
        $sql = "DELETE FROM fm_marker WHERE field<=8 AND field_id=$card_id";
        $data[] = $this->dbhandle->execute($sql);
        return $data;
    }

    function addComment($uid, $proj_id, $field, $field_id, $content){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $field = intval($field);
        $field_id = intval($field_id);
        $content = $this->dbhandle->escape($content);
        $sql = "INSERT INTO fm_comment(commentor_id, proj_id, field, field_id, content) VALUES($uid, $proj_id, $field, $field_id, '$content')";
        $data = $this->dbhandle->execute($sql);
        $this->resetMarker($uid, $proj_id, $field, $field_id);
        return $data;
    }

    function deleteComment($comment_id){
        $comment = intval($comment_id);
        $sql = "DELETE FROM fm_comment WHERE comment_id=$comment_id";
        $data = array();
        $data[] = $this->dbhandle->execute($sql);
        $sql = "DELETE FROM fm_reply WHERE comment_id=$comment_id";
        $data[] = $this->dbhandle->execute($sql);
        return $data;
    }

    function addReply($uid, $comment_id, $content){
        $uid = intval($uid);
        $comment_id = intval($comment_id);
        $content = $this->dbhandle->escape($content);
        $sql = "INSERT INTO fm_reply(comment_id, replier_id, content) VALUES($comment_id, $uid, '$content')";
        $data = $this->dbhandle->execute($sql);
        $sql = "SELECT * FROM fm_comment WHERE comment_id=$comment_id";
        $result = $this->dbhandle->query($sql);
        $proj_id = $result[0]['proj_id'];
        $field = $result[0]['field'];
        $field_id = $result[0]['field_id'];
        $this->resetMarker($uid, $proj_id, $field, $field_id);
        return $data;
    }

    function deleteReply($reply_id){
        $reply_id = intval($reply_id);
        $sql = "DELETE FROM fm_reply WHERE reply_id=$reply_id";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function updateMarker ($uid, $proj_id, $field, $field_id) {
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $field = intval($field);
        $field_id = intval($field_id);
        $sql = "INSERT INTO fm_marker(uid, proj_id, field, field_id, status) VALUES($uid, $proj_id, $field, $field_id, 1)";
        $data = $this->dbhandle->execute($sql);
        if (!is_int($data)) {
            $sql = "UPDATE fm_marker SET status=1 WHERE uid=$uid AND proj_id=$proj_id AND field=$field ANd field_id=$field_id";
            $data = $this->dbhandle->execute($sql);
        }
        return $data;
    }

    function resetMarker ($uid, $proj_id, $field, $field_id) {
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $field = intval($field);
        $field_id = intval($field_id);
        $sql = "UPDATE fm_marker SET status=0 WHERE proj_id=$proj_id AND field=$field AND field_id=$field_id AND uid!=$uid";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function getMarker ($uid, $proj_id) {
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $sql = "SELECT pulse_id AS field, pulse_no AS field_id FROM fm_pulse WHERE proj_id=$proj_id";
        $result1 = $this->dbhandle->query($sql);
        $sql = "SELECT DISTINCT canvas_id AS field FROM fm_canvas WHERE proj_id=$proj_id";
        $result2 = $this->dbhandle->query($sql);
        for ($i = 0; $i < count($result2); $i++) {
            $result2[$i]['field_id'] = 0;
        }
        $sql = "SELECT canvas_id AS field, card_id AS field_id FROM fm_card WHERE proj_id=$proj_id";
        $result3 = $this->dbhandle->query($sql);
        $sql = "SELECT DISTINCT field, field_id FROM fm_comment WHERE proj_id=$proj_id";
        $result4 = $this->dbhandle->query($sql);
        $values = array_merge($result1, $result2, $result3, $result4);
        for ($i = 0; $i < count($values); $i++) {
            for ($j = $i + 1; $j < count($values); $j++) {
                $diff = array_diff($values[$i], $values[$j]);
                if (!$diff) {
                    unset($values[$j]);
                    $j--;
                }
            }
        }
        $sql = "SELECT field, field_id FROM fm_marker WHERE uid=$uid AND proj_id=$proj_id AND status=1";
        $marker = $this->dbhandle->query($sql);
    /*print_r($marker);
    echo 'hello<br>';
    print_r($values);
    echo 'world<br>';*/
        for ($i = 0; $i < count($marker); $i++) {
            for ($j = 0; $j < count($values); $j++) {
                $diff = array_diff($marker[$i], $values[$j]);
                if (!$diff) {
                    unset($values[$j]);
                    $values = array_merge($values);
                    break;
                }
            }
        }
        //print_r($values);
        $temp = $this->getArrayColumn($values, 'field');
        $temp = array_unique($temp);
    /*$test = $result[0];
    $record = array();
    foreach($result as $value) {
      $record = array_merge($this->getArrayColumn($value, 'field'), $record);
    }
    $record = array_unique($record);
    $marker = $this->getArrayColumn($marker, 'field');
    $record = array_diff($record, $marker);
    $data = array();*/
        $data = array();
        foreach($temp as $value) {
            switch($value) {
            case 1:
                $data['detail'] = 1;
                break;
            case 2:
                $data['painpoint'] = 1;
                break;
            case 3:
                $data['value'] = 1;
                break;
            case 4:
                $data['scheme'] = 1;
                break;
            case 5:
                $data['indicator'] = 1;
                break;
            case 6:
                $data['income'] = 1;
                break;
            case 7:
                $data['channel'] = 1;
                break;
            case 8:
                $data['growth'] = 1;
                break;
            case 11:
                $data['week'] = 1;
                break;
            case 12:
                $data['month'] = 1;
                break;
            case 13:
                $data['season'] = 1;
                break;
            }
        }
        return $data;
    }

    function getArrayColumn(array $array, $column_key, $index_key=null){
        $result = array();
        foreach($array as $arr) {
            if(!is_array($arr)) continue;
            if(is_null($column_key)){
                $value = $arr;
            }else{
                $value = $arr[$column_key];
            }
            if(!is_null($index_key)){
                $key = $arr[$index_key];
                $result[$key] = $value;
            }else{
                $result[] = $value;
            }
        }
        return $result; 
    }
}
