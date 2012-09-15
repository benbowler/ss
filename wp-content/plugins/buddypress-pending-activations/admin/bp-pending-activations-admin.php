<?php 

function etivite_bp_pending_activations_users() {
	global $wpdb;
		
	return $wpdb->get_results( $wpdb->prepare( "SELECT u.*, m1.meta_value as activation_key FROM $wpdb->usermeta m1, $wpdb->users u WHERE u.ID = m1.user_id AND u.user_status = 2 AND m1.meta_key = 'activation_key' ORDER BY u.user_registered ASC" ) );
}

function etivite_bp_pending_activations_users_resent( $user_id ) {
	global $wpdb;
	
	return $wpdb->get_row( $wpdb->prepare( "SELECT meta_value as activation_key_resent FROM $wpdb->usermeta WHERE meta_key = 'activation_key_resent' AND user_id = %d", $user_id ) );
}

function etivite_bp_pending_activations_admin() {
	global $bp, $wpdb;

	if ( is_multisite() )
		wp_die( __('This plugin is for single WordPress installs only w/BuddyPress') );

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('etivite_bp_pending_activations_admin') ) {
	
		if ( isset($_POST['action'] ) && !empty( $_POST['action'] ) && !empty( $_POST['users'] ) ) {
		
			$userids = $_POST['users'];
		
			if ( $_POST['action'] == 'delete' ) {

				if ( !current_user_can( 'delete_users' ) )
					wp_die(__('You can&#8217;t delete users.'));

				foreach ( (array) $userids as $id) {
				
					$id = (int) $id;

					if ( !current_user_can( 'delete_user', $id ) )
						wp_die(__( 'You can&#8217;t delete that user.' ) );

					if ( is_super_admin( $id ) )
						wp_die(__( 'You can&#8217;t delete that user.' ) );

					if ( $id == $current_user->ID )
						wp_die(__( 'You can&#8217;t delete that user.' ) );

					wp_delete_user($id);

				}
			
				wp_cache_delete( 'etivite_bp_pending_activations_count' );
				$deleteupdated = true;
			
			} else if ( $_POST['action'] == 'resend' ) {

				if ( is_array( $userids ) )
					$userids = implode( ',', $userids );

				$userids = $wpdb->escape( $userids );

				if ( empty( $userids ) )
					wp_die(__( 'No users selected' ) );

				$resendusers = $wpdb->get_results( $wpdb->prepare( "SELECT m.meta_value, u.ID, u.user_email FROM $wpdb->usermeta m, $wpdb->users u WHERE u.ID = m.user_id AND m.meta_key = 'activation_key' AND u.ID IN ({$userids})" ) );

				foreach ( $resendusers as $resend ) {

					bp_core_signup_send_validation_email( $resend->ID, $resend->user_email, $resend->meta_value );
					
					update_user_meta( $resend->ID, 'activation_key_resent', gmdate('Y-m-d H:i:s') );

				}
			
				$resendupdated = true;
			
			} else if ( $_POST['action'] == 'activate' ) {

				if ( is_array( $userids ) )
					$userids = implode( ',', $userids );

				$userids = $wpdb->escape( $userids );

				if ( empty( $userids ) )
					wp_die(__( 'No users selected' ) );

				$resendusers = $wpdb->get_results( $wpdb->prepare( "SELECT u.ID, u.user_login, m.meta_value FROM $wpdb->usermeta m, $wpdb->users u WHERE u.ID = m.user_id AND m.meta_key = 'activation_key' AND u.ID IN ({$userids})" ) );

				foreach ( $resendusers as $resend ) {

					/* Activate the signup */
					$user = apply_filters( 'bp_core_activate_account', bp_core_activate_signup( $resend->meta_value ) );

					/* If there was errors, add a message and redirect */
					if ( !empty( $user->errors ) )
						echo 'There was an error activating this account, please try again: '. $resend->user_login;

					/* Check if the avatar folder exists. If it does, move rename it, move it and delete the signup avatar dir */
					if ( file_exists( bp_core_avatar_upload_path() . '/avatars/signups/' . wp_hash( $user ) ) )
						@rename( bp_core_avatar_upload_path() . '/avatars/signups/' . wp_hash( $user ), bp_core_avatar_upload_path() . '/avatars/' . $user );
					
				}
			
				wp_cache_delete( 'etivite_bp_pending_activations_count' );
				$activateupdated = true;
			
			}
			
		}
	}
	
	// Get the proper URL for submitting the settings form. (Settings API workaround) - boone
	$url_base = function_exists( 'is_network_admin' ) && is_network_admin() ? network_admin_url( 'admin.php?page=bp-pending-activations-settings' ) : admin_url( 'admin.php?page=bp-pending-activations-settings' );
	
?>	
	<div class="wrap">
	
		<div class="icon32" id="icon-users"><br></div>
		
		<h2><?php echo sprintf( __('Pending Activations (%s) Admin'), etivite_bp_pending_activations_users_count() ); ?></h2>

		<?php if ( isset($deleteupdated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Users Deleted.', 'bp-pending-activations' ) . "</p></div>"; endif; ?>
		<?php if ( isset($resendupdated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Resent Activation Keys.', 'bp-pending-activations' ) . "</p></div>"; endif; ?>
		<?php if ( isset($activateupdated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Users Activated.', 'bp-pending-activations' ) . "</p></div>"; endif; ?>

		<form action="<?php echo $url_base ?>" name="bp-pending-activations-settings-form" id="bp-pending-activations-settings-form" method="post">

			<div class="tablenav">

				<div class="alignleft actions">
					<select name="action">
						<option selected="selected" value="">Bulk Actions</option>
						<option value="delete">Delete User</option>
						<option value="resend">Resend Key</option>
						<option value="activate">Activate</option>
					</select>
					<input type="submit" class="button-secondary action" id="submit" name="submit" value="Apply">
				</div>

				<br class="clear">
			</div>

			<table cellspacing="0" class="widefat fixed">
			
				<thead>
				<tr class="thead">
					<th class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
					<th class="manage-column column-username" id="username" scope="col">Username</th>
					<th class="manage-column column-name" id="name" scope="col">Name</th>
					<th class="manage-column column-email" id="email" scope="col">E-mail</th>
					<th class="manage-column column-date" id="date" scope="col">Date Registered</th>
					<th class="manage-column column-date" id="rdate" scope="col">Date Resent</th>
				</tr>
				</thead>

				<tfoot>
				<tr class="thead">
					<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
					<th class="manage-column column-username" scope="col">Username</th>
					<th class="manage-column column-name" scope="col">Name</th>
					<th class="manage-column column-email" scope="col">E-mail</th>
					<th class="manage-column column-date" scope="col">Date Registered</th>
					<th class="manage-column column-date" scope="col">Date Resent</th>
				</tr>
				</tfoot>

				<tbody class="list:user user-list" id="users">

				<?php
				$pending_users = etivite_bp_pending_activations_users();

				foreach ( $pending_users as $user ) { ?>

					<tr class="alternate" id="user-<?php echo $user->ID; ?>">
					 <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $user->ID; ?>" id="user_<?php echo $user->ID; ?>" name="users[]"></th>
					 <td class="username column-username"><img width="32" height="32" class="avatar" alt="" src="http://www.gravatar.com/avatar/<?php echo md5( strtolower( $user->email ) ) . '?&amp;s=32'; ?>"> <?php echo $user->user_login; ?></td>
					 <td class="name column-name"><?php echo $user->display_name; ?></td>
					 <td class="email column-email"><?php echo $user->user_email; ?></td>
					 <td class="date column-date"><?php echo bp_core_time_since( $user->user_registered ); ?></td>
					 <td class="date column-date"><?php $resent = etivite_bp_pending_activations_users_resent($user->ID );

					 if ( $resent ) echo bp_core_time_since( $resent->activation_key_resent ); ?></td>
					</tr>

				<?php
				}
				?>
					
				</tbody>

			</table>
			
			<?php wp_nonce_field( 'etivite_bp_pending_activations_admin' ); ?>
			
		</form>
		
		<h3>About:</h3>
		<div id="plugin-about" style="margin-left:15px;">
			
			<p>
			<a href="http://etivite.com/wordpress-plugins/buddypress-pending-activations/">Pending Activations - About Page</a><br/> 
			</p>
		
			<div class="plugin-author">
				<strong>Author:</strong> <a href="http://profiles.wordpress.org/users/etivite/"><img style="height: 24px; width: 24px;" class="photo avatar avatar-24" src="http://www.gravatar.com/avatar/9411db5fee0d772ddb8c5d16a92e44e0?s=24&amp;d=monsterid&amp;r=g" alt=""> rich @etivite</a><br/>
				<a href="http://twitter.com/etivite">@etivite</a>
			</div>
		
			<p>
			<a href="http://etivite.com">Author's site</a><br/>
			<a href="http://etivite.com/api-hooks/">Developer Hook and Filter API Reference</a><br/>
			<a href="http://etivite.com/wordpress-plugins/">WordPress Plugins</a><br/>
			</p>
		</div>
		
		
	</div>
<?php
}

?>
