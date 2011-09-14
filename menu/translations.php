<?php

$posts = get_posts(-1);

?>

<div class="wrap">
  <div id="icon-edit-pages" class="icon32"><br /></div>
  <h2>Translation Status</h2>
</div>

<?php
foreach($posts as &$post) {
  echo '<li>';
  echo $post->post_title;
  echo '<form id="wti_push_post" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
  echo '<input type="hidden" name="wti_post_id" value="'.$post->ID.'" />';
  echo '<input type="hidden" name="action" value="push" />';
  echo '<input type="submit" value="push" />';
  echo '</form>';
  echo '<form id="wti_push_post" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
  echo '<input type="hidden" name="wti_post_id" value="'.$post->ID.'" />';
  echo '<input type="hidden" name="action" value="pull" />';
  echo '<input type="submit" value="pull" />';
  echo '</form>';
  echo '</li>';
}
?>