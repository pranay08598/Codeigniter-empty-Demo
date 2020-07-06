<?php
/*
+------------------------+
--response code--
1 => Success
2 => failyer
3 => data missing
4 => exception
--status code--
200 - ok
201 - created
204 - no content
304 - not modified
400 - bad request
401 - unauthorized
404 - not found
422 - unprocessable entity
500 - internal server error
+------------------------+
*/
class RestHandler {

    public static function restResponse($code,$message,$result,$statuscode) {
        $data["ResponseCode"]=$code;
        $data["ResponseMessage"]="$message";
        $data["Result"]=$result;
        $data["Statuscode"]=$statuscode;
        // $data["TimeZone"]=date('T');
        echo json_encode($data);
    }

    public static function restMessage($code,$message,$statuscode) {
        $data["ResponseCode"]=$code;
        $data["ResponseMessage"]="$message";
        $data["Statuscode"]=$statuscode;
        // $data["TimeZone"]=date('T');
        echo json_encode($data);

    }
    public static function ResponseFormErrorr($error)
    {
        $error = explode("</p>", $error);
        $er=str_replace("<p>", "", $error[0]);
        $er=str_replace("</p>", "", $er);
        $er=str_replace("\n", "", $er);
        $er=str_replace("_", " ", $er);

        RestHandler::restMessage(2,$er,'402');
    }
}
?>
