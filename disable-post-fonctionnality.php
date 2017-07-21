<?php
/*
Plugin Name: Disable Post Functionnality
Description: Disabled all the Post Functionnality
Author: Aurélien Chappard
Author URI: http://www.deefuse.fr/
Version: 1.0
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


if (!class_exists("DisablePostFuncionnality")) {
	class DisablePostFuncionnality {
		
		/* Hide Post Menu from the main menu admin */
		function deletePostFromAdminMenu()
		{
			global $menu;
				$restricted = array( __('Posts'));
				end ($menu);
				while (prev($menu)){
					$value = explode(' ',$menu[key($menu)][0]);
					if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
				}
		}
		
		/* 
		* Remove the standard dashboard widget 'right_now' 
		* Create new widget based on the standard
		*/
		function create_new_dashboard_widgets_right_now()
		{
			// delete the current widget
			remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
			
			//add a new widget
			wp_add_dashboard_widget('dashboard_right_nowNew', __( 'Right Now' ), array($this,'dashboard_widget_right_now_lite'));
		}
		
		/* 
		*  Disable the standard Right Now widget et create new widget based on 
		*  the function wp_dashboard_right_now() describe on /wp-admin/includes/dashboard.php file
		*/

		function dashboard_widget_right_now_lite()
		{
			global $wp_registered_sidebars;

			$num_posts = wp_count_posts( 'post' );
			$num_pages = wp_count_posts( 'page' );
		
			$num_cats  = wp_count_terms('category');
		
			$num_tags = wp_count_terms('post_tag');
		
			$num_comm = wp_count_comments( );
		
			echo "\n\t".'<div class="table table_content">';
			echo "\n\t".'<p class="sub">' . __('Content') . '</p>'."\n\t".'<table>';
			echo "\n\t".'<tr class="first">';
		
			// Pages
			$num = number_format_i18n( $num_pages->publish );
			$text = _n( 'Page', 'Pages', $num_pages->publish );
			if ( current_user_can( 'edit_pages' ) ) {
				$num = "<a href='edit.php?post_type=page'>$num</a>";
				$text = "<a href='edit.php?post_type=page'>$text</a>";
			}
			echo '<td class="first b b_pages">' . $num . '</td>';
			echo '<td class="t pages">' . $text . '</td>';
		
			echo '</tr><tr>';
		
			
		
			// Tags
			$num = number_format_i18n( $num_tags );
			$text = _n( 'Tag', 'Tags', $num_tags );
			if ( current_user_can( 'manage_categories' ) ) {
				$num = "<a href='edit-tags.php'>$num</a>";
				$text = "<a href='edit-tags.php'>$text</a>";
			}
			echo '<td class="first b b-tags">' . $num . '</td>';
			echo '<td class="t tags">' . $text . '</td>';
		
			echo "</tr>";
			do_action('right_now_content_table_end');
			echo "\n\t</table>\n\t</div>";
		
		
			echo "\n\t".'<div class="table table_discussion">';
			echo "\n\t".'<p class="sub">' . __('Discussion') . '</p>'."\n\t".'<table>';
			echo "\n\t".'<tr class="first">';
		
			// Total Comments
			$num = '<span class="total-count">' . number_format_i18n($num_comm->total_comments) . '</span>';
			$text = _n( 'Comment', 'Comments', $num_comm->total_comments );
			if ( current_user_can( 'moderate_comments' ) ) {
				$num = '<a href="edit-comments.php">' . $num . '</a>';
				$text = '<a href="edit-comments.php">' . $text . '</a>';
			}
			echo '<td class="b b-comments">' . $num . '</td>';
			echo '<td class="last t comments">' . $text . '</td>';
		
			echo '</tr><tr>';
		
			// Approved Comments
			$num = '<span class="approved-count">' . number_format_i18n($num_comm->approved) . '</span>';
			$text = _nx( 'Approved', 'Approved', $num_comm->approved, 'Right Now' );
			if ( current_user_can( 'moderate_comments' ) ) {
				$num = "<a href='edit-comments.php?comment_status=approved'>$num</a>";
				$text = "<a class='approved' href='edit-comments.php?comment_status=approved'>$text</a>";
			}
			echo '<td class="b b_approved">' . $num . '</td>';
			echo '<td class="last t">' . $text . '</td>';
		
			echo "</tr>\n\t<tr>";
		
			// Pending Comments
			$num = '<span class="pending-count">' . number_format_i18n($num_comm->moderated) . '</span>';
			$text = _n( 'Pending', 'Pending', $num_comm->moderated );
			if ( current_user_can( 'moderate_comments' ) ) {
				$num = "<a href='edit-comments.php?comment_status=moderated'>$num</a>";
				$text = "<a class='waiting' href='edit-comments.php?comment_status=moderated'>$text</a>";
			}
			echo '<td class="b b-waiting">' . $num . '</td>';
			echo '<td class="last t">' . $text . '</td>';
		
			echo "</tr>\n\t<tr>";
		
			// Spam Comments
			$num = number_format_i18n($num_comm->spam);
			$text = _nx( 'Spam', 'Spam', $num_comm->spam, 'comment' );
			if ( current_user_can( 'moderate_comments' ) ) {
				$num = "<a href='edit-comments.php?comment_status=spam'><span class='spam-count'>$num</span></a>";
				$text = "<a class='spam' href='edit-comments.php?comment_status=spam'>$text</a>";
			}
			echo '<td class="b b-spam">' . $num . '</td>';
			echo '<td class="last t">' . $text . '</td>';
		
			echo "</tr>";
			do_action('right_now_table_end');
			do_action('right_now_discussion_table_end');
			echo "\n\t</table>\n\t</div>";
		
			echo "\n\t".'<div class="versions">';
			$ct = current_theme_info();
		
			echo "\n\t<p>";
		
			if ( empty( $ct->stylesheet_dir ) ) {
				if ( ! is_multisite() || is_super_admin() )
					echo '<span class="error-message">' . __('ERROR: The themes directory is either empty or doesn&#8217;t exist. Please check your installation.') . '</span>';
			} elseif ( ! empty($wp_registered_sidebars) ) {
				$sidebars_widgets = wp_get_sidebars_widgets();
				$num_widgets = 0;
				foreach ( (array) $sidebars_widgets as $k => $v ) {
					if ( 'wp_inactive_widgets' == $k || 'orphaned_widgets' == substr( $k, 0, 16 ) )
						continue;
					if ( is_array($v) )
						$num_widgets = $num_widgets + count($v);
				}
				$num = number_format_i18n( $num_widgets );
		
				$switch_themes = $ct->title;
				if ( current_user_can( 'switch_themes') )
					$switch_themes = '<a href="themes.php">' . $switch_themes . '</a>';
				if ( current_user_can( 'edit_theme_options' ) ) {
					printf(_n('Theme <span class="b">%1$s</span> with <span class="b"><a href="widgets.php">%2$s Widget</a></span>', 'Theme <span class="b">%1$s</span> with <span class="b"><a href="widgets.php">%2$s Widgets</a></span>', $num_widgets), $switch_themes, $num);
				} else {
					printf(_n('Theme <span class="b">%1$s</span> with <span class="b">%2$s Widget</span>', 'Theme <span class="b">%1$s</span> with <span class="b">%2$s Widgets</span>', $num_widgets), $switch_themes, $num);
				}
			} else {
				if ( current_user_can( 'switch_themes' ) )
					printf( __('Theme <span class="b"><a href="themes.php">%1$s</a></span>'), $ct->title );
				else
					printf( __('Theme <span class="b">%1$s</span>'), $ct->title );
			}
			echo '</p>';
		
			// Check if search engines are blocked.
			if ( !is_network_admin() && !is_user_admin() && current_user_can('manage_options') && '1' != get_option('blog_public') ) {
				$title = apply_filters('privacy_on_link_title', __('Your site is asking search engines not to index its content') );
				$content = apply_filters('privacy_on_link_text', __('Search Engines Blocked') );
		
				echo "<p><a href='options-privacy.php' title='$title'>$content</a></p>";
			}
		
			update_right_now_message();
		
			echo "\n\t".'<br class="clear" /></div>';
			do_action( 'rightnow_end' );
			do_action( 'activity_box_end' );

		}
		
		function add_cssToWidget()
		{
			?>
			<style type="text/css">
			#dashboard_right_nowNew p.sub,#dashboard_right_nowNew .table,#dashboard_right_nowNew .versions{margin:-12px;}#dashboard_right_nowNew .inside{font-size:12px;padding-top:20px;}#dashboard_right_nowNew p.sub{padding:5px 0 15px;color:#8f8f8f;font-size:14px;position:absolute;top:-17px;left:15px;}#dashboard_right_nowNew .table{margin:0;padding:0;position:relative;}#dashboard_right_nowNew .table_content{float:left;border-top:#ececec 1px solid;width:45%;}#dashboard_right_nowNew .table_discussion{float:right;border-top:#ececec 1px solid;width:45%;}#dashboard_right_nowNew table td{padding:3px 0;white-space:nowrap;}#dashboard_right_nowNew table tr.first td{border-top:none;}#dashboard_right_nowNew td.b{padding-right:6px;text-align:right;font-size:14px;width:1%;}#dashboard_right_nowNew td.b a{font-size:18px;}#dashboard_right_nowNew td.b a:hover{color:#d54e21;}#dashboard_right_nowNew .t{font-size:12px;padding-right:12px;padding-top:6px;color:#777;}#dashboard_right_nowNew .t a{white-space:nowrap;}#dashboard_right_nowNew .spam{color:red;}#dashboard_right_nowNew .waiting{color:#e66f00;}#dashboard_right_nowNew .approved{color:green;}#dashboard_right_nowNew .versions{padding:6px 10px 12px;clear:both;}#dashboard_right_nowNew a.button{float:right;clear:right;position:relative;top:-5px;}
			</style>
			<?php
		}

	}//End Class DisablePostFuncionnality
	
}


if (class_exists("DisablePostFuncionnality")) {
	$disablePostFuncionnality_plugin = new DisablePostFuncionnality();
}

//Actions and Filters	
if (isset($disablePostFuncionnality_plugin))
{
	//Actions
	add_action('admin_menu', array(&$disablePostFuncionnality_plugin, 'deletePostFromAdminMenu'));
	add_action('wp_dashboard_setup', array(&$disablePostFuncionnality_plugin, 'create_new_dashboard_widgets_right_now') );
	add_action('admin_head', array(&$disablePostFuncionnality_plugin, 'add_cssToWidget'));
	
	
	//Filters
}

?>