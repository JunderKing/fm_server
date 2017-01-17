<?php

class Model{
    protected $dbhandle;
    public function __construct(){
        $dbhandle = new Database();
    }

    function getSessionId($code){
        $appId = "wx04a548f574c826fb";
        $appSecret = "68f1ef93cc6c9153e8d49b95899aa9a2";
        $url = "https://api.weixin.qq.com/sns/jscode2session?" . 
            "appid=" . appId . 
            "&secret=" . appSecret . 
            "&js_code=" . code . 
            "&grant_type=authorization_code";
        $resJson = file_get_contents($url);
        $resArr = json_decode($resJson);
        if (is_int($resArr->errcode)) {
            printf ("login failed: %s\n", $resArr->errmsg);
            exit();
        }
        $sessionId = strval(mt_rand(100000, 999999));
        session_start();
        $_SESSION[$sessionId] = $resArr;
        return array($sessionId=>$resArr);
    }

    function createGroup($uid, $title){
        $uid = intval($uid);
        $time = time();
        $title = $this->dbhandle->escape($title);
        $sql = "INSERT INTO fm_group(group_id, uid, title, ctime) VALUES(null, $uid, $title, $time)";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function updateGroupInfo($group_id, $title){
        $group_id = intval($group_id);
        $title = $this->dbhandle->eacape($title);
        $sql = "UPDATE fm_group SET title=$title WHERE group_id=$group_id";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function addGroupProj($group_id, $proj_id){
        $group_id = intval($group_id);
        $proj_id = intval($proj_id);
        $sql = "INSET INTO fm_group_proj(group_id, proj_id) VALUES($group_id, $proj_id)";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function deleteGroupProj($group_id, $proj_id){
        $group_id = intval($group_id);
        $proj_id = intval($proj_id);
        $sql = "DELETE FROM fm_group_proj WHERE group_id=$group_id AND proj_id=$proj_id";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function createProj($uid, $role, $title, $intro, $mission, $vision, $value){
        $creator_id = intval($uid);
        $role = intval($role);
        $title = $this->dbhandle->escape($title);
        $intro = $this->dbhandle->escape($intro);
        $mission = $this->dbhandle->escape($mission);
        $vision = $this->dbhandle->escape($vision);
        $value = $this->dbhandle->escape($value);
        $data = array();
        $sql = "INSERT INTO fm_project(creator_id, title, intro, mission, vision, value) VALUES($creator_id, $title, $intro, $mission, $vision, $value)";
        $data[] = $this->dbhandle->execute($sql);
        $proj_id = $this->dbhandle->insert_id;
        $sql = "INSERT INTO fm_proj_member(uid, proj_id, role) VALUES($creator_id, $proj_id, $role)";
        $data[] = $this->dbhandle->execute($sql);
        $sql = "INSERT INTO fm_canvas(proj_id) VALUES($proj_id)";
        $data[] = $this->dbhandle->execute($sql);
        return $data;
    }

    function updateProjInfo($proj_id, $title, $intro, $mission, $vision, $value){
        $proj_id = intval($proj_id);
        $title = $this->dbhandle->escape($title);
        $intro = $this->dbhandle->escape($intro);
        $mission = $this->dbhandle->escape($mission);
        $vision = $this->dbhandle->escape($vision);
        $value = $this->dbhandle->escape($value);
        $sql = "UPDATE fm_project SET creator_id=$creator_id, title=$title, intro=$intro, mission=$mission, vision=$vision, value=$value WHERE proj_id=$proj_id";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function getProjInfo($proj_id){
        $proj_id = intval($proj_id);
        $sql = "SELECT * FROM fm_project WHERE proj_id=$proj_id";
        $data = $this->dbhandle->query($sql);
        return $data;
    }

    function addProjMember($proj_id, $uid, $role){
        $proj_id = intval($proj_id);
        $uid = intval($uid);
        $sql = "INSERT INTO fm_proj_member(uid, proj_id, role) VALUES($uid, $proj_id, $role)";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function deleteProjMember($proj_id, $uid){
        $proj_id = intval($proj_id);
        $uid = intval($uid);
        $sql = "DELETE FROM fm_proj_member WHERE proj_id=$proj_id AND uid=$uid";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function getWeekReport($proj_id){
        $proj_id = intval($proj_id);
        $sql = "SELECT * FROM fm_wkreport WHERE proj_id=$proj_id";
        $data = $this->dbhandle->query($sql);
        return $data;
    }

    function updateWeekReport($uid, $proj_id, $week_no, $content){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $week_no = intval($int_no);
        $content = $this->dbhandle->escape($content);
        $sql = "INSERT INTO fm_wkreport(proj_id, week_no, content, redactor_id) VALUES($proj_id, $week_no, $content, $uid)";
        $data = $this->dbhandle->execute($sql);
        if (!is_int($data)) {
            $sql = "UPDATE fm_wkreport SET content=$content, redactor_id=$uid WHERE proj_id=$proj_id AND week_no=$week_no";
            $data = $this->dbhandle->execute($sql);
        }
        return $data;
    }

    function getMonthRecord($proj_id){
        $proj_id = intval($proj_id);
        $sql = "SELECT * FROM fm_mthrecord WHERE proj_id=$proj_id";
        $data = $this->dbhandle->query($sql);
        return $data;
    }

    function updateMonthRecord($uid, $proj_id, $month_no, $content, $pdf_url){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $month_no = intval($month_no);
        $content = $this->dbhandle->escape($content);
        $pdf_url = $this->dbhandle->escape($pdf_url);
        $sql = "INSERT INTO fm_mthrecord(redactor_id, proj_id, month_no, content, pdf_url), VALUES($uid, $proj_id, $month_no, $content, $pdf_url)";
        $data = $this->dbhandle->execute($sql);
        if (!is_int($data)) {
            $sql = "UPDATE fm_mthrecord SET redactor_id=$uid, content=$content, pdf_url=$pdf_url WHERE proj_id=$proj_id AND month_no=$month_no";
            $data = $this->dbhandle->execute($sql);
        }
        return $data;
    }

    function getSummary($proj_id){
        $proj_id = intval($proj_id);
        $sql = "SELECT * FROM fm_summary WHERE proj_id=$proj_id";
        $data = $this->dbhandle->query($sql);
        return $data;
    }

    function updateSummary($uid, $proj_id, $content, $photo_url){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $content = $this->dbhandle->escape($content);
        $photo_url = $this->dbhandle->escape($photo_url);
        $sql = "INSERT INTO fm_summary(redactor_id, proj_id, content, photo_url) VALUES($uid, $proj_id, $content, $photo_url)";
        $data = $this->dbhandle->execute($sql);
        if (!is_int($data)) {
            $sql = "UPDATE fm_summary SET redactor_id=$uid, content=$content, photo_url=$photo_url WHERE proj_id=$proj_id";
            $data = $this->dbhandle->execute($sql);
        }
        return $data;
    }

    function getCanvasInfo($proj_id){
        $proj_id = intval($proj_id);
        $sql = "SELECT * FROM fm_canvas WHERE proj_id=$proj_id";
        $data = $this->dbhandle->query($sql);
        return $data;
    }

    function updateCanvaeInfo($proj_id, $field, $content){
        $proj_id = intval($proj_id);
        $field = $this->dbhandle->escape($field);
        $content = $this->dbhandle->escape($content);
        $sql = "UPDATE fm_canvas SET $field=$content WHERE proj_id=$proj_id";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function addCard($proj_id, $canvas_id, $title, $assumption){
        $proj_id = intval($proj_id);
        $canvas_id = intval($canvas_id);
        $title = $this->dbhandle->escape($title);
        $assumption = $this->dbhandle->escape($assumption);
        $sql = "INSERT INTO fm_card(proj_id, canvas_id, title, assumption) VALUES($proj_id, $canvas_id, $title, $assumption)";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }
    
    function getCardInfo($proj_id, $canvas_id){
        $proj_id = intval($proj_id);
        $canvas_id = intval($canvas_id);
        $sql = "SELECT * FROM fm_card WHERE proj_id=$proj_id AND canvas_id=$canvas_id";
        $data = $this->dbhandle->query($sql);
        return $data;
    }

    function updateCardInfo($card_id, $title, $assumption, $result){
        $card_id = intval($card_id);
        $title = $this->dbhandle->escape($title);
        $assumption = $this->dbhandle->escape($assumption);
        $result = $this->dbhandle->escape($result);
        $sql = "UPDATE fm_card SET title=$title, assumption=$assumption, result=$result WHERE card_id=$card_id";
        $data = $this->dbhandle->execute($sql);
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
        return $data;
    }

    function addComment($uid, $proj_id, $target_id, $target_type, $content){
        $uid = intval($uid);
        $proj_id = intval($proj_id);
        $target_id = intval($target_id);
        $target_type = intval($target_type);
        $content = $this->dbhandle->escape($content);
        $sql = "INSERT INTO fm_comment(commentor_id, proj_id, target_id, target_type, content) VALUES($uid, $proj_id, $target_id, $target_type, $content)";
        $data = $this->dbhandle->execute($sql);
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
        $sql = "INSERT INTO fm_reply(comment_id, replier_id, content) VALUES($comment_id, $uid, $content)";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function deleteReply($reply_id){
        $reply_id = intval($reply_id);
        $sql = "DELETE FROM fm_reply WHERE reply_id=$reply_id";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function addCardComment($uid, $card_id, $content){
        $uid = intval($uid);
        $card_id = intval($card_id);
        $content = $this->dbhandle->escape($content);
        $sql = "INSERT INTO fm_card_comment(commentor_id, card_id, content) VALUES($uid, $card_id, $content)";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }

    function deleteCardComment($comment_id){
        $comment_id = intval($comment_id);
        $sql = "DELETE FROM fm_card_comment WHERE comment_id=$comment_id";
        $data = array();
        $data[] = $this->dbhandle->execute($sql);
        $sql = "DELETE FROM fm_card_reply WHERE comment_id=$comment_id";
        $data[] = $this->dbhandle->execute($sql);
        return $data;
    }

    function addCardReply($uid, $comment_id, $content){
        $uid = intval($uid);
        $comment_id = intval($comment_id);
        $content = $this->dbhandle->escape($sql);
        $sql = "INSERT INTO fm_card_reply(replier_id, comment_id, content) VALUES($uid, $comment_id, $content)";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }
    
    function deleteCardReply($reply_id){
        $reply_id = intval($reply_id);
        $sql = "DELETE FROM fm_card_reply WHERE reply_id=$reply_id";
        $data = $this->dbhandle->execute($sql);
        return $data;
    }
}
