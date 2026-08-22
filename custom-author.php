<?php
/*
Plugin Name: 	Custom Author
Plugin URI: 	https://wpstore.app/archives/custom-author/
Description: 	自定义作者插件
Version: 		1.2
Author: 		Bestony
Author URI: 	https://wpstore.app/
License: 		GPL2
License URI:  	https://www.gnu.org/licenses/gpl-2.0.html
 */
/*  Copyright  2018 Bestony (email : xiqingongzi@gmail.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

add_action('post_submitbox_misc_actions', 'cus_author_createCustomField');
add_action('save_post', 'cus_author_saveCustomField');
/** 创建一个checkBox */
function cus_author_createCustomField() {
	$post_id = get_the_ID();
	if (get_post_type($post_id) != 'post') {
		return;
	}
	/**
	 * 提取现有的值
	 * @var boolean
	 */
	$value = get_post_meta($post_id, '_custom_author_name', true);
	/**
	 * 添加 nonce 安全处理
	 */
	wp_nonce_field('custom_author_nonce', 'custom_author_nonce');
	?>
    <div class="misc-pub-section misc-pub-section-last dashicons-before dashicons-admin-users">
        <label><b>作者：</b><input type="text" value="<?php echo $value ?>" name="_custom_author_name" /></label>
    </div>
    <?php
}
/**
 * 保存配置信息
 * @param  int $post_id 文章的ID
 */
function cus_author_saveCustomField($post_id) {
	/**
	 * 自动保存不处理
	 */
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	/**
	 * nonce 信息不正确不处理
	 */
	if (
		!isset($_POST['custom_author_nonce']) ||
		!wp_verify_nonce($_POST['custom_author_nonce'], 'custom_author_nonce')
	) {
		return;
	}
	/**
	 * 用户无权编辑文件不处理
	 */
	if (!current_user_can('edit_post', $post_id)) {
		return;
	}
	/**
	 * 存在此项目就更新
	 */
	if (isset($_POST['_custom_author_name'])) {
		update_post_meta($post_id, '_custom_author_name', sanitize_text_field($_POST['_custom_author_name']));
	} else {
		/**
		 * 不存在就删除
		 */
		delete_post_meta($post_id, '_custom_author_name');
	}
}

add_filter('the_author', 'cus_author_the_author');
function cus_author_the_author($author) {
	$custom_author = get_post_meta(get_the_ID(), '_custom_author_name');
	if ($custom_author[0]) {
		return $custom_author[0];
	} else {
		return $author;
	}
}