<?php

$posts = get_posts(-1);

foreach($posts as &$post) {
  echo '<li><a href="'.$_SERVER['REQUEST_URI'].'?action=push&id='.$post->ID.'">';
  echo $post->post_title;
  echo "</a></li>";
}

?>