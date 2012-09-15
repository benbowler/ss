<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class contactme_application_form extends wv48fv_action {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __construct($application, $array = null) {
		$name = 'default';
		$this->slug = $application->slug;
		if(is_array($array))
		{
			if (null !== $array['data']) {
				$name = $array['data'];
			}
			if (null !== $array['slug']) {
				$this->slug = $array['slug'];
			}
		}
		$this->form = $name;
		parent::__construct ( $application );
	}
	public $slug=null;
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $form = null;
	public function form() {
		return $this->form;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function &fields() {
		return $this->cache ( 'contactme_application_form_fields', $this->form (), true );
	}
	public function data()
	{
		return parent::data($this->form);
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function error() {
		return $this->fields ()->error ();
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	function render() {
		$return = '';
		$general = $this->data()->general;
		//print_r($this->data()->form());
		// tweak the screen type with new known exceptions
		if(is_feed())
		{
				$general['screen']='rss';
		}
		elseif($general['screen']==='open' && !is_user_logged_in() && $general['privacy']==='private')
		{
			$general['screen']='private';
		}
		elseif($general['screen']==='results' && $this->error () !== 0)
		{
			$general['screen']='error';
		}elseif(isset ( $_COOKIE [$this->application ()->slug . '_' . strtolower ( $this->form() ).'_'.$this->user()->user_login] ))
		{
			$general['screen']='cookie';
		}	
		// get all the rendered fields for each output type.
		$data = $this->fields ()->render ($general['screen']);
		$this->do_email($data);
		$this->do_post($data);
		$this->do_table($data);
		$return = '';		
		// if this its not rss then send the output to the form
		if(count($data->rss)===0)
		{
			$this->view->form_fields = implode('',$data->front);
			$this->view->form_name = $this->form ();
			$this->view->form_action = get_permalink ();
			$this->view->form_error = $this->fields ()->error ();
			$return  = trim ( $this->render_script ( 'blog/form.phtml', false ), "\n\r " );
		}
		else
		{
			// display the rss fields. ie error
			$return = implode('',$data->rss);
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function recipient_email() {
		$return = get_the_author_meta ( 'user_email' );
		if (! empty ( $this->data ()->data_collection ['email'] ['address'] )) {
			$return = $this->data ()->data_collection ['email'] ['address'];
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
public function do_email($data) {
		if(count($data->email)===0)
		{
			return;
		}
		// this still needs tweaking.
		$subject = get_the_title ();
		$recipient = $this->recipient_email ();
		$mail = new wv48fv_mail ( false );
		$message = implode("\n",$data->email);
		foreach ( $data->email_details as $email ) {
			$from = $email->name ['value'] . " <" . $email->email ['value'] . ">";
			$mail->send ( $this->recipient_email (), $subject, $message, $from );
			break;
		}
		$subject='CC: '.$subject;
		foreach ( $data->email_details as $email ) {
			if ($email->cc['value']) {
				$from = $email->name ['value'] . " <" . $email->email ['value'] . ">";
				$mail->send ( $email->email ['value'], $subject, $message, $from );
			}
		}
		return '';
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function do_post($data) {
		if(count($data->post)===0)
		{
			return;
		}
		$post = implode("\n",$data->post);
		$newpost = null;
		if (isset ( $this->data ()->data_collection ['post'] ['name'] )) {
			$newpost = get_page_by_title ( $this->data ()->data_collection ['post'] ['name'], OBJECT, 'post' );
		}
		if (is_null ( $newpost )) {
			$newpost = get_default_post_to_edit ();
			$newpost->post_content = '<ul class="submit"></ul>';
		}
		// remove get old submissions from post without ul tags
		$pattern = '|<ul class="submit">([\w\W\s\S]*)</ul>|Ui';
		preg_match_all ( $pattern, $newpost->post_content, $matches, PREG_SET_ORDER );
		$old_content = "";
		foreach ( $matches as $match ) {
			$old_content = $match [1];
			break;
		}
		$newpost->post_title = $this->data ()->data_collection ['post'] ['name'];
		$newpost->post_author = get_the_author_meta ( 'ID' );
		$newpost->post_content = '<ul class="submit">' . $old_content . '<li>' . $post . '</li></ul>';
		$id = wp_update_post ( $newpost );
		return '';
	}

/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function do_table($data) {
		if(count($data->table->data)===0)
		{
			return;
		}
		$table = strtolower ( $this->application ()->slug . '_' . strtolower ( $this->form () ) );
		$this->fields = array ('id' => array ('type' => 'bigint(20)', 'null' => false, 'extra' => 'AUTO_INCREMENT' ) );
		$this->keys = array ('primary' => 'id' );
		$this->table ( $table )->create_table ( $this->fields, $this->keys );
		$results = $this->table ( $table )->show_columns ();
		$columns = array ();
		foreach ( $results as $result ) {
			$columns [] = $result ['Field'];
		}
		foreach ( $data->table->type as $key => $value ) {
			if (! in_array ( $key, $columns )) {
				$this->table ( $table )->alter_table ( $key, $value );
			}
		}
		$sender = null;
		foreach ( $data->email_details as $sender ) {
			break;
		}
		$where = '';
		if (null !== $sender) {
			$where = " `%s` = '%s' OR ";
			$this->table ()->object_name ( $sender->email ['question'] );
			$where = sprintf ( $where, $sender->email ['question'], $sender->email ['value'] );
		}
		// if this user has submitted, mark the previous as older, buy both email and userid
		$sql = "
UPDATE `%s`
	SET `info[submission]` = `info[submission]`+1
WHERE %s 
	(`info[wp_user]`=%d AND`info[wp_user]`!=-1)
";
		$sql = sprintf ( $sql, $this->table ( $table )->name (), $where, $data->table->data ['info[wp_user]'] );
		$this->table ( $table )->insert ( $data->table->data );
		$this->table()->execute($sql);
	}	
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function forms($show_hidden = false,$saved_only=false) {
		if ($this->request ()->is_post ()) {
			$src = '';
			$dst = '';
			$del = array ();
			$del_table = array ();
			if (isset ( $_POST ['source_setting'] )) {
				$src = $_POST ['source_setting'];
			}
			if (isset ( $_POST ['new_form'] )) {
				$dst = $_POST ['new_form'];
				$dst = $this->data()->filter_name($dst);
			}
			if (isset ( $_POST ['delete_setting'] )) {
				$del = $_POST ['delete_setting'];
			}
			if (isset ( $_POST ['delete_table'] )) {
				$del_table = $_POST ['delete_table'];
			}
			if (! empty ( $src ) && ! empty ( $dst )) {
				$this->data ()->copy ( $dst, $src );
			}
			foreach ( $del as $d ) {
				$this->data ()->delete ( $d );
			}
			foreach ( $del_table as $d ) {
				$this->table ($d)->drop (  );
			}
		}
		$forms = $this->data ()->forms ( $show_hidden,$saved_only );
		return $forms;
	}
}