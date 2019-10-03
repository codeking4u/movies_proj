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
    $page=1;
    $per_page=6;
    $sql = "select DISTINCT movies.* from movies";
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
    $full_res = mysqli_query($conn, $sql); 
    $total_pages = ceil($full_res->num_rows/$per_page);
    
    if(isset($_REQUEST['curr-page'])&&($_REQUEST['curr-page']>0)){
        $page = $_REQUEST['curr-page'];
    }
	$offset= ($page*$per_page)-$per_page;
	$sql .= " LIMIT  $offset, $per_page ";
    $result = mysqli_query($conn, $sql);
    ob_start();
    while( $row = mysqli_fetch_assoc( $result)){ ?>
        <div class="card" style="width: 30rem">
        <img src="<?php echo $row['featured_image']?>" class="card-img-top" alt="..." style="width: 100%">
        <div class="card-body overlay">
            <div class="overlay-content">
            <h3 class="title"><?php echo $row['title']?></h3>
            <p class="text"><?php echo $row['description']?></p>
            <h6><?php echo 'Release Date : ' .$row['release_date']?></h6>
            </div>
        </div>
        <h5 class="card_title"><?php echo $row['title']?></h5>
        <div class="detail-main">
            <div class="card-detail">
                <div class="genre_title">GENRE: <?php echo get_taxonomy_name($row['id'],'genre')?></div>
                <div class="language_title">LANGUAGE: <?php echo get_taxonomy_name($row['id'],'language')?></div>
            </div>
            <div class="card-detail">
                <div class="likes">LIKES: <?php echo $row['likes'];?></div>
                <div class="duration">DURATION: <?php echo $row['duration_in_min'].' mins';?></div>
            </div>
        </div>
    </div>
    <?php
    }
    $movie_data = ob_get_clean();
    echo json_encode(array('total_page'=>$total_pages,'data'=>$movie_data));
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
function get_taxonomy_name($movie_id,$taxonomy_type){
    global $conn;
    $res = array();
    $sql = "select tl.title from term_movie_relationships 
    JOIN term_list tl ON tl.term_id =term_movie_relationships.term_id
    JOIN taxonomy_list ta ON ta.id = tl.taxonomy_id
    where term_movie_relationships.movie_id = $movie_id AND ta.taxonomy_name='$taxonomy_type'";
    $result = mysqli_query($conn, $sql);
    while($row = $result->fetch_assoc()){
        $res[] = $row['title'];
    }
    return implode(',',$res);
}

?>

