<?php
/* function to get menu with parent of $parent_code */
function menuList ($parent_code="0"){
    $link = DB_LOCATION;
    $params = array (
        "action" => "retrieve-menu",
        "fileToOpen" => "retrieve_menu_display",
        "parent_code" => $parent_code,
        "user_code" => $_SESSION["user_code"],
        "dbconnect" => MONEYTRACKER_DB
    );
    $result=processCurl($link,$params);

    $output = json_decode($result,true);
    if($output[0]["result"]==='1'){
        return $output;
    }
    else {
        return NULL;
    }
}


/* function to get menu with parent of $parent_code disregarding access type (for user privilege) */
function menuListAll ($parent_code="0"){
    $link = DB_LOCATION;
    $params = array (
        "action" => "retrieve",
        "fileToOpen" => "default_select_query",
        "tableName" => "mstmenuitem",
        "dbconnect" => MONEYTRACKER_DB,
        "columns" => "code,module_mst_code,menu_item_mst_code,is_active,menu_name,type" ,
        "conditions[equals][menu_item_mst_code]" => $parent_code,
        "conditions[equals][is_active]" => "1",
        "orderby" => "order_no ASC"
    );
    $result=processCurl($link,$params);

    $output = json_decode($result,true);
    if($output[0]["result"]==='1'){
        return $output;
    }
    else {
        return NULL;
    }
}

/* recursive - check if the parent can be displayed */
function canDisplay ($parent_code="0"){
    $temp = false;
    if ($parent_code=="0"){
        return false;
    }
    else {
        $menu = menuList($parent_code);
        if (!is_null($menu)){
            foreach ($menu as $key => $value){
                if ($value["type"]=="1"){
                    //return canDisplay($value["code"]);
                    //return true;
                    //$temp = canDisplay($value["code"]);
                    if ($temp==false){
                        $temp = canDisplay($value["code"]);
                    }
                }
                else {
                    return true;
                }
            }
            //return false;
            return $temp;
        }
        else {
            return false;
        }
    }
}

function canDisplayNonRecursive ($parent_code="0"){
    $temp = false;
    if ($parent_code=="0"){
        return false;
    }
    else {
        $menu = menuList($parent_code);
        if (!is_null($menu)){
            foreach ($menu as $key => $value){
                if ($value["type"]=="1"){
                    //return canDisplay($value["code"]);
                    //return true;
                    //$temp = canDisplay($value["code"]);
                    /*if ($temp==false){
                        $temp = canDisplay($value["code"]);
                    }*/
                }
                else {
                    return true;
                }
            }
            //return false;
            return $temp;
        }
        else {
            return false;
        }
    }
}


/* recursive - check if the parent can be displayed disregarding access type (for user privilege) */
function canDisplayAll ($parent_code="0"){
    $temp = false;
    if ($parent_code=="0"){
        return false;
    }
    else {
        $menu = menuListAll($parent_code);
        if (!is_null($menu)){
            foreach ($menu as $key => $value){
                if ($value["type"]=="1"){
                    //return canDisplay($value["code"]);
                    //return true;
                    //$temp = canDisplay($value["code"]);
                    if ($temp==false){
                        $temp = canDisplayAll($value["code"]);
                    }
                }
                else {
                    return true;
                }
            }
            //return false;
            return $temp;
        }
        else {
            return false;
        }
    }
}

function canDisplayNonRecursiveAll ($parent_code="0"){
    $temp = false;
    if ($parent_code=="0"){
        return false;
    }
    else {
        $menu = menuListAll($parent_code);
        if (!is_null($menu)){
            foreach ($menu as $key => $value){
                if ($value["type"]=="1"){
                    //return canDisplay($value["code"]);
                    //return true;
                    //$temp = canDisplay($value["code"]);
                    /*if ($temp==false){
                        $temp = canDisplay($value["code"]);
                    }*/
                }
                else {
                    return true;
                }
            }
            //return false;
            return $temp;
        }
        else {
            return false;
        }
    }
}
?>