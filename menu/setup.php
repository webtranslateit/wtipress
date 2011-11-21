<?php
  require_once WTIPRESS_PLUGIN_PATH . '/wtipress.php';
?>

<div class="wrap">
  <div id="icon-options-general" class="icon32"><br /></div>
  <h2>Setup WTIpress</h2>
    
  <form id="wti_api_key" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <label for="wti_api_key">WebTranslateIt.com API key</label>
          </th>
          <td>
            <input type="text" name="wti_api_key" id="wti_api_key" value="<?php echo $wtipress->settings->api_key ?>" />
            <span class="description">You will find your API key in your project settings.</span>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="wti_translation_missing">When a translation is missing</label>
          </th>
          <td>
            <select id="wti_translation_missing" name="wti_translation_missing">
              <option value="display_source_language"<?php if ($wtipress->settings->action_missing_translation == "display_source_language") echo "selected " ?>>
                Display post in source language
              </option>
              <option value="dont_display_post"<?php if ($wtipress->settings->action_missing_translation == "dont_display_post") echo "selected " ?>>
                Don’t display post
              </option>
            </select>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="language_negociation_format">Language URL format</label>
          </th>
          <td>
            <select id="language_negociation_format" name="language_negociation_format">
              <option value="param"<?php if ($wtipress->settings->language_negociation_format == "param") echo "selected " ?>>
                Using a ?lang=xx parameter
              </option>
              <option value="directory"<?php if ($wtipress->settings->language_negociation_format == "directory") echo "selected " ?>>
                Using directories www.blog.com for English, www.blog.com/fr for French...
              </option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    
    <p class="submit">
      <input class="button-primary" name="save" value="Save and Refresh" type="submit" />
    </p>
  </form>
  
  <?php
  if(isset($wtipress->settings->api_key)) {
    echo '<div class="updated fade"><p><strong>WTIpress is setup correctly</strong>';
    echo ' and can sync with the project “<a href="https://webtranslateit.com/projects/'.$wtipress->settings->project_id.'">'.$wtipress->settings->project_name.'</a>” on WebTranslateIt.com. Good job!</p></div>';
  ?>
  
  <h3>Languages at play</h3>

  <p>
    Tip: Add languages on your <?php echo '<a href="https://webtranslateit.com/projects/'.$wtipress->settings->project_id.'">project on WebTranslateIt.com</a> and refresh this form for them to appear.'; ?>
    We detected the following languages:
  </p>
  
  <table class="wp-list-table widefat fixed languages" cellspacing="0">
  	<thead>
  	<tr>
  		<th scope='col' id='name' class='manage-column column-name'><span>Language Name</span></th>
  		<th scope='col' id='type' class='manage-column column-type'>Type</th>
  		<th scope='col' id='actions' class='manage-column column-type'>Actions</th>
    </tr>
    </thead>
  	<tbody id="the-list">
  	  <?php $source_language = Language::get_source_language(); ?>
  	  <tr id='post-8' class='alternate author-self status-publish format-default iedit' valign="top">
  	    <td class="language-name page-title column-title"><strong><?php echo $source_language[0]->name; ?></a></strong></td>
        <td class="date column-type">Source</td>
        <td class="date column-actions"></td>
      </tr>
      <?php foreach(Language::get_all() as $language) { ?>
        <tr id='post-8' class='alternate author-self status-publish format-default iedit' valign="top">
    	    <td class="language-name page-title column-title"><strong><?php echo $language->name; ?></a></strong></td>
          <td class="date column-type">Target</td>
          <td class="date column-actions">
            <form id="wti_api_key" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
              <input type="hidden" name="remove_language_code" value="<?php echo $language->code; ?>" />
              <input type="submit" value="Remove Language" />
            </form>
          </td>
        </tr>
    	<?php }  ?>
    </tbody>
  </table>
  <form id="wti_api_key" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <div>
      <label for="new_language">Add a new language</label>
      <input type="text" name="new_language" id="new_language" />
    </div>
    <div>
      <input type="submit" />
    </div>
  </form>
  <?php } ?>
</div>