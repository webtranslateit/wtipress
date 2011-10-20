<?php

if (isset($_GET['display']) && $_GET['display'] == 'pages') {
  $posts = get_posts(array('numberposts' => -1, 'post_type' => 'page'));
}
else {
  $posts = get_posts(array('numberposts' => -1, 'post_type' => ''));
}

?>

<div class="wrap">
  <div id="icon-edit-pages" class="icon32"><br /></div>
  <h2>Translation Status</h2>
  <ul class="subsubsub">
    <li><a href="?page=wtipress/menu/translations.php" <?php if (!isset($_GET['display']) || $_GET['display'] != 'pages') { echo "class='current'"; } ?>>Posts</a> | </li>
    <li><a href="?page=wtipress/menu/translations.php&display=pages" <?php if (isset($_GET['display']) && $_GET['display'] == 'pages') {echo "class='current'";} ?>>Pages</a></li>
  </ul>
</div>

<table class="wp-list-table widefat fixed posts" cellspacing="0">
	<thead>
	<tr>
		<th scope='col' id='title' class='manage-column column-title'><span>Title</span></th>
		<th scope='col' id='categories' class='manage-column column-categories'>Created at</th>
		<th scope='col' id='categories' class='manage-column column-categories'>Last pushed at</th>
		<th scope='col' id='categories' class='manage-column column-categories'>Last pulled at</th>
  </tr>
  </thead>
	<tbody id="the-list">
	  <?php foreach($posts as &$post) { ?>
	  <tr id='post-8' class='alternate author-self status-publish format-default iedit' valign="top">
	    <td class="post-title page-title column-title"><strong><a class="row-title" href="post.php?post=<?php echo $post->ID; ?>&amp;action=edit"><?php echo $post->post_title; ?></a></strong></td>
      <td class="date column-date">
        <?php echo distance_of_time_in_words(strtotime($post->post_date), time()); ?> ago
        <?php if ($post->post_modified != $post->post_date) { echo "<br /> Updated ". distance_of_time_in_words(strtotime($post->post_modified), time()) . " ago"; } ?>
      </td>
      <?php
        $translations = Translation::get_translations_for_post($post);
      ?>
	    <td class="date column-date">
	      <?php
	        if (empty($translations)) { echo "Never"; }
          else { echo distance_of_time_in_words(strtotime($translations[0]->last_pushed_at), time()) . " ago"; }
          echo '<form id="wti_push_post" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
          echo '<input type="hidden" name="wti_post_id" value="'.$post->ID.'" />';
          echo '<input type="hidden" name="action" value="push" />';
          echo '<input type="submit" value="Push" class="button-primary" />';
          echo '</form>';
        ?>
      </td>
	    <td class="date column-date">
	      <?php
	        if (empty($translations) || ($translations[0]->last_pulled_at == "0000-00-00 00:00:00" || $translations[0]->last_pulled_at == NULL)) { echo "Never"; }
          else { echo distance_of_time_in_words(strtotime($translations[0]->last_pulled_at), time()) . " ago"; }
          echo '<form id="wti_push_post" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
          echo '<input type="hidden" name="wti_post_id" value="'.$post->ID.'" />';
          echo '<input type="hidden" name="action" value="pull" />';
          echo '<input type="submit" value="Pull" class="button-primary" />';
          echo '</form>';
        ?>
      </td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php
?>