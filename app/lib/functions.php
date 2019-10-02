<?php
require __DIR__ .'/../config/db.php';
if(isset($_REQUEST['action']) && function_exists($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
    call_user_func($action);
}
function get($param , $def=""){
    return isset($_REQUEST[$param]) ? $_REQUEST[$param] : $def;
}


function get_movies(){
    global $conn;
    $sql = "select DISTINCT movies.* from movies";
        
    /* if(isset($_REQUEST['langs']) && isset($_REQUEST['genres']) ){
        $res_array = array_merge( $_REQUEST['langs'] , $_REQUEST['genres'] );
        $join = " JOIN term_movie_relationships ";
    }else */
    $join = "";
    $where = "";
    if(isset($_REQUEST['langs'])){
        $lang_list = implode(',',$_REQUEST['langs']);
        $join .= " INNER JOIN term_movie_relationships AS langTerms
        ON movies.id = langTerms.movie_id
        INNER JOIN term_list AS list_lang 
        ON langTerms.term_id = list_lang.term_id AND list_lang.taxonomy_id = 2 ";
        $where .= " list_lang.term_id IN ($lang_list) ";
        
    } 
    if(isset($_REQUEST['genres'])){
        $genres_list = implode(',',$_REQUEST['genres']);
        $join .= " INNER JOIN term_movie_relationships AS genreTerms
        ON movies.id = genreTerms.movie_id
        INNER JOIN term_list AS list_genre 
        ON genreTerms.term_id = list_genre.term_id AND list_genre.taxonomy_id = 1 ";
        if($where != ""){
            $where .= " AND ";
        }
        $where .= " list_genre.term_id IN ($genres_list) ";
    }
    if(empty($join)){
        $join = " LEFT JOIN term_movie_relationships ON movies.id = term_movie_relationships.movie_id ";
        //THis is used when there is no filter. Purposely done to show all movies irrespective of genre or lang info present in DB or not
    }
    $sql .= $join;
    if(!empty($where)){
        $sql .= " WHERE ".$where;
    }
    if(isset($_REQUEST['sort_by'])){
        if($_REQUEST['sort_by']=='length'){
            $sql .= " ORDER BY movies.duration_in_min ASC ";
        }else{
            $sql .= " ORDER BY movies.likes DESC ";
        }
    }
    $result = mysqli_query($conn, $sql);
    ob_start();
    while( $row = mysqli_fetch_assoc( $result)){ ?>
        <div class="card" style="width: 30rem">
        <img src="<?php echo $row['featured_image']?>" class="card-img-top" alt="..." style="width: 100%">
        <div class="card-body overlay">
            <div class="overlay-content">
            <h3 class="title"><?php echo $row['title']?></h3>
            <p class="text"><?php echo $row['description']?></p>
            <h6><?php echo $row['release_date']?></h6>
            </div>
        </div>
        <h5 class="card_title"><?php echo $row['title']?></h5>
    </div>
    <?php
    }
    $variable = ob_get_clean();
    echo $variable;
    //echo json_encode($new_array);
}

function get_titles($type){
    global $conn;
    $sql = "SELECT * FROM term_list where taxonomy_id=$type";
		$result = mysqli_query($conn, $sql);
		while($row = $result->fetch_assoc()){
			$res[$row['term_id']] = $row['title'];
		}
		return $res;
}

?>

