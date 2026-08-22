<?php
/**
 * Tests for Custom Author.
 */
class Custom_Author_Test extends WP_UnitTestCase {

	public function test_hooks_registered() {
		$this->assertNotFalse( has_action( 'save_post', 'cus_author_saveCustomField' ) );
		$this->assertNotFalse( has_filter( 'the_author', 'cus_author_the_author' ) );
	}

	public function test_save_custom_author_meta() {
		$user_id = self::factory()->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $user_id );
		$post_id = self::factory()->post->create( array( 'post_author' => $user_id ) );

		$_POST['custom_author_nonce'] = wp_create_nonce( 'custom_author_nonce' );
		$_POST['_custom_author_name'] = 'Ada Lovelace';

		cus_author_saveCustomField( $post_id );

		$this->assertSame( 'Ada Lovelace', get_post_meta( $post_id, '_custom_author_name', true ) );
	}

	public function test_save_skips_invalid_nonce() {
		$user_id = self::factory()->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $user_id );
		$post_id = self::factory()->post->create( array( 'post_author' => $user_id ) );

		$_POST['custom_author_nonce'] = 'nope';
		$_POST['_custom_author_name'] = 'Ignored';

		cus_author_saveCustomField( $post_id );

		$this->assertSame( '', get_post_meta( $post_id, '_custom_author_name', true ) );
	}

	public function test_the_author_returns_custom_name() {
		$post_id = self::factory()->post->create();
		update_post_meta( $post_id, '_custom_author_name', 'Guest Writer' );
		$this->go_to( get_permalink( $post_id ) );
		setup_postdata( get_post( $post_id ) );

		$this->assertSame( 'Guest Writer', cus_author_the_author( 'Original Author' ) );
	}

	public function test_the_author_falls_back_without_meta() {
		$post_id = self::factory()->post->create();
		$this->go_to( get_permalink( $post_id ) );
		setup_postdata( get_post( $post_id ) );

		$this->assertSame( 'Original Author', cus_author_the_author( 'Original Author' ) );
	}
}
