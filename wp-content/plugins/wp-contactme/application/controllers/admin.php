<?php
/*****************************************************************************************
* all the admin pages
*****************************************************************************************/
class contactme_admin extends wv48fv_action {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function FormsEditActionMeta($return) {
		if(isset($_GET ['edit']))
		{
			$return ['title'] = 'Forms&raquo;Edit&raquo;' . $_GET ['edit'] ;
		}
		$return ['hide'] = true;
		return $return;
	}
	public function upgradeWPnoticeMeta($return)
	{
		if(isset($_GET['page']) && $_GET['page']==$this->application()->slug && $this->application()->slug!='contactme' && !isset($_GET['edit']))
		{
			$return['alert']='error';
		}
		else
		{
			$return['title']='';
		}
		return $return;
	}
	public function upgradeWPnotice()
	{
		return $this->render_script('upgrade.phtml');
	}
	public function importWPnoticeMeta($return)
	{
		if(isset($_GET['page']) && $_GET['page']=='contactme' && !isset($_GET['edit']) && $this->application()->slug=='contactme')
		{
			
			$slugs = array('pollme','quizme','submission','surveyme');
			$show = false;
			$return['alert']='error';
			foreach($slugs as $slug)
			{
				$iforms = $this->form(null,$slug)->forms(false,true);
				if(count($iforms)>0)
				{
					$show=true;
					break;
				}
			}
			if(!$show || get_option('contactme_imported')=='done')
			{
				$return['title']='';
			}
		}
		else
		{
			$return['title']='';
		}
		return $return;
	}
	public function importWPnotice()
	{
		return $this->render_script('import.phtml');
	}
	public function FormsEditAction() {
		$this->view->body = '';
		$data = $this->data ( $_GET ['edit'] )->post ();
		$this->view->general = $data['general'];
		$this->view->data_collection = $data['data_collection'];
		$this->view->graphics = $data['graphics'];
		$this->view->field_definitions = $data['field_definitions'];
		$this->view->views = $data['views'];
		$return = '';
		$recaptcha = new contactme_recaptcha ( $this->application () );
		$recaptcha_keys = $data['recaptcha_keys'];
		if(isset($_POST['recaptcha_keys']['public']))
		{
			$this->view->recaptcha_keys = $this->data ( '_keys' )->post ( 'recaptcha_keys' );
		}
		else
		{
			$this->view->recaptcha_keys = $this->data ( '_keys' )->recaptcha_keys;
		}
		$this->view->recaptcha_keys['theme']=$recaptcha_keys['theme'];
		$this->view->signup_url = $recaptcha->signup_url ();
		$this->view->url = $this->dashboard ( 'Settings', $this->application ()->name )->url;
		$this->view->title = $this->help('fields')->render('Fields');
		$this->view->column_count=2;
		$this->view->return_url = $this->dashboard ( 'Settings', $this->application ()->name )->url;
		$this->view->table_type='standard field_definitions_'.$_GET ['edit'];
		$return = '';
		$this->view->body = '<ul class="v48fv_field_definitions">';
		$return_url=$this->view->return_url;
		$this->view->apply='';
		//$this->view->columns=false;
		$this->view->rows=array();
		$this->view->return_url=null;
		$this->view->footer=false;
		$this->view->columns=false;
		$this->view->table_type='top';
		$this->view->body .= '<li>'.$this->render_table().'</li>';
		$this->view->title=null;
		$this->view->cnt=0;
		foreach($this->view->field_definitions as $value){
			if($value['fixed_field']===null)
			{
				$this->view->cnt=$value['field'];
			}
		}
		$this->view->columns = false;
		$this->view->alternate = '';
		unset($this->view->table_type);
		$middle =' middle';
		foreach($this->view->field_definitions as $key=>$value){
			$this->view->rows=array();
			$this->view->apply='';
			$this->view->return_url=null;
			$this->view->key=$key;
			$this->view->value=$value;
			$this->view->table_type='v48fv_'.$value['type']."{$middle}";
			if($value['type']!='new')
			{
				{
					$this->view->columns =  $this->render_script('settings/field_definitions/row-header.phtml',false);
				}
				if(in_array($value['type'],array('cookie')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/cookie/row-duration.phtml',false);
					if($this->view->general['config']!='simple')
					{
						$this->view->rows[] = $this->render_script('settings/field_definitions/cookie/row-response.phtml',false);
					}
				}
				if(in_array($value['type'],array('number_captcha','cc','checkbox','radio','text','textarea','name','link','quiz_question','poll_question','email','submit')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/row-concise_prompt.phtml',false);
				}
				if(in_array($value['type'],array('checkbox','radio','text','textarea','link','quiz_question','poll_question')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/row-expanded_prompt.phtml',false);
				}
				if(in_array($value['type'],array('textstring')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/textstring/message.phtml',false);
				}
				if(in_array($value['type'],array('checkbox','radio','text','quiz_question','poll_question')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/row-options.phtml',false);
				}
				if(in_array($value['type'],array('quiz_question')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/quiz_question/row-answer.phtml',false);
				}
				if(in_array($value['type'],array('poll_question')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/poll_question/width.phtml',false);
					if($this->view->general['config']!='simple')
					{
						$this->view->rows[] = $this->render_script('settings/field_definitions/poll_question/type.phtml',false);
						$this->view->rows[] = $this->render_script('settings/field_definitions/poll_question/colors.phtml',false);
					}
				}
				if(in_array($value['type'],array('recaptcha')))
				{	
					if($this->view->recaptcha_keys['private']=='' && $this->view->recaptcha_keys['public']=='')
					{
						$this->view->rows[] = $this->render_script('settings/field_definitions/recaptcha/signup.phtml',false);
					}
					$this->view->rows[] = $this->render_script('settings/field_definitions/recaptcha/public_key.phtml',false);
					$this->view->rows[] = $this->render_script('settings/field_definitions/recaptcha/private_key.phtml',false);
					if(in_array($this->view->general,array('simple')))
					{
						$this->view->rows[] = $this->render_script('settings/field_definitions/recaptcha/theme.phtml',false);
					}
				}
				if(in_array($value['type'],array('results_header')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/headers/results.phtml',false);
				}
				if(in_array($value['type'],array('post_header')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/headers/post.phtml',false);
				}
				if(in_array($value['type'],array('email_header')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/headers/email.phtml',false);
				}
				if(in_array($value['type'],array('quiz_header')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/headers/quiz.phtml',false);
				}
				if(in_array($value['type'],array('closed_header')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/headers/closed.phtml',false);
				}
				if(in_array($value['type'],array('pending_header')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/headers/pending.phtml',false);
				}
				if(in_array($value['type'],array('private_footer')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/footers/private.phtml',false);
				}
				if(in_array($value['type'],array('rss_header')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/headers/rss.phtml',false);
				}
				if(in_array($value['type'],array('email_footer')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/footers/email.phtml',false);
				}
				if($this->view->general['config']==='debug' || !in_array($this->view->general['config'],array('simple','intermediate')) && !in_array($value['type'],array('results_header','post_header','email_header','quiz_header','closed_header','pending_header','rss_header','email_footer','private_footer','cookie','number_captcha','recaptcha')))
				{	
					$this->view->rows[] = $this->render_script('settings/field_definitions/row-add_to.phtml',false);
				}
				if(!(in_array($value['type'],array('private_footer','results_header','post_header','email_header','quiz_header','closed_header','pending_header','rss_header','email_footer','formsent','wpuser')) && in_array($this->view->general['config'],array('simple','intermediate'))) && !(in_array($value['type'],array('thankyou')) && in_array($this->view->general['config'],array('simple'))))
				{
					$this->view->body .= '<li>'.$this->render_table().'</li>';
				}
				$middle = ' middle';
			}
		}
		$this->view->apply='';
		unset($this->view->return_url);
		$this->view->rows=array();
		unset($this->view->columns);
//		$this->view->body .= '<li>'.$this->render_table().'</li>';
		$this->view->body.='</ul>';
		$this->view->rows[] =  $this->render_script('settings/general/row1.phtml',false);
		$this->view->rows[] =  $this->render_script('settings/general/row2.phtml',false);
		$this->view->rows[] =  $this->render_script('settings/general/row3.phtml',false);
		$this->view->types = $this->types($_GET ['edit']);
		$this->view->rows[] =  $this->render_script('settings/field_definitions/row-new.phtml',false);
		$this->view->sidebar = '<ul>';
		$this->view->table_type='v48fv_general';
		$this->view->title = $this->help('general')->render('General');
		$this->view->sidebar .= '<li>'.$this->render_table().'</li>';
		if(!in_array($this->view->general['config'],array('simple')) && !in_array($this->application()->slug,array('pollme','quizme')))
		{
			$this->view->return_url=null;
			$this->view->rows=array();
			$this->view->rows[] =  $this->render_script('settings/general/row8.phtml',false);
			$this->view->rows[] =  $this->render_script('settings/general/row9.phtml',false);
			$this->view->rows[] =  $this->render_script('settings/general/row10.phtml',false);
			$this->view->rows[] =  $this->render_script('settings/general/row11.phtml',false);
			$this->view->title = $this->help('data_collection')->render('Data Collection');
			$this->view->sidebar .= '<li>'.$this->render_table().'</li>';
		}
		$this->view->rows=array();
		unset($this->view->apply);
		$this->view->title = null;
		$this->view->return_url = $return_url;
		$this->view->footer=false;
		$this->view->columns=false;
		$this->view->table_type='top';
		$this->view->sidebar .= '<li>'.$this->render_table().'</li>';
		$this->view->sidebar .= '</ul>';		
		$page = $this->render_script('settings/field_definitions/page.phtml',false);	
		return $page;
	}
/*****************************************************************************************
* choose the settings section and set the link title
*****************************************************************************************/
	public function surveyWPmenuMeta($return) {
		$return ['menu'] = 'Settings';
		$return ['slug'] = $this->application ()->slug;
		$return ['title'] = $this->application ()->name;
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function FormsActionMeta($return) {
		$return ['link_name'] = $return ['title'];
		$return ['classes'] [] = 'v48fv_16x16_form';
		$return ['priority'] = - 1;
		return $return;
	}
	public function FormsAction() {
		$this->view->forms = $this->form ()->forms ();
		$forms=$this->view->forms;
		$this->view->has_tables = false;
		foreach ( $this->view->forms as $form ) {
			if (null !== $form ['table']) {
				$this->view->has_tables = true;
				break;
			}
		}
		//$this->pre();
		//print_r($this->data()->data());
		//$this->pre();
		$this->view->title = $this->help('forms')->render('Forms');
		$this->view->column_count=6;
		$this->view->table_type='survey_list '.$this->data ( )->general['config'];
		$this->view->columns = $this->render_script('settings/forms/columns.phtml',false);
		$this->view->cnt=0;
		foreach ( $this->get_actions ( 'wpmenu' ) as $menu ) {
			break;
		}
		$baseUrl = $this->dashboard ( $menu ['menu'], $menu ['title'] )->url;
		$edit = $this->dashboard ( $menu ['menu'], $menu ['title'] )->url.'&page2=FormsEdit&edit=';
		foreach($forms as $form)
		{
			
			$this->view->form=$form;
			$this->view->used_in=$this->used_in($form ['name'],$this->application()->slug,'form'); 
			$this->view->edit= $edit.urlencode ( $form ['name'] );
			$this->view->rows[] = $this->render_script('settings/forms/row.phtml',false);
		}		

		$this->view->footer = $this->render_script('settings/forms/footer.phtml',false);
		$page = $this->render_table();


		//$page .=$this->render_script ( 'common/forms.phtml',false );
		return $page;
	}
	public function do_importWPnoticeMeta($return)
	{
		if(!isset($_GET['survey_import']))
		{
			$return['title']='';
		}
		return $return;
	}
	public function do_importWPnotice()
	{
		if(!isset($_GET['survey_import']))
		{
			return null;
		}
		set_time_limit(120);
		$shortcodes = array();
		$slugs = array('pollme','quizme','submission','surveyme');
		//$slugs = array('surveyme');
		foreach($slugs as $slug)
		{
			$forms = $this->form()->forms();
			$iforms = $this->form(null,$slug)->forms(false,true);
			$cnt=2;
			foreach($iforms as $key=>&$value)
			{
				$cnt=2;
				$name = str_replace('$','',$key);
				while(isset($forms[$name]))
				{
					$name = str_replace('$','',$key);
					$name.=$cnt;
					$cnt++;
				}
				$value['name']=$key;
				$value['new_name']=$name;
				$form = $this->data($key,$slug)->data();
				$this->data($name)->writePost($form);
				$this->data(null,$slug)->delete($name);
				if($value['table']!='')
				{
					$sql="RENAME TABLE `%s` TO `%s`;";
					$sql=sprintf($sql,$this->table("{$slug}_{$value['name']}")->name(),$this->table("contactme_{$value['new_name']}")->name());
					$this->table()->execute($sql);
				}
			}
			unset($value);
			$shortcodes=$this->find_shortcodes($slug,'form');
			foreach($shortcodes as $key=>$value1)
			{
					foreach($value1 as $value2)
					{
						$post = get_post($value2['ID'],ARRAY_A);
						if(isset($iforms[$key]))
						{
							$key=$iforms[$key]['new_name'];
						}
						$new = "[contactme {$key}]";
						$post['post_content'] = str_replace ( $value2 ['match'], $new, $post['post_content'] );
						$err=wp_update_post( $post );
					}
			}
		}
		$this->data()->set_slug($this->application()->slug);
		update_option('contactme_imported','done');
		return "Your forms have been imported";
	}
/*****************************************************************************************
* work out which types user is allowed to add and which types are still available to add
*****************************************************************************************/
	public function types($form)
	{
		$fields = $this->data($form)->field_definitions;
		$data_collection = $this->data($form)->data_collection;
		$types = $this->application()->settings['types'];
		// remove all the ones that are not allowed to be inserted ( only added by the plugin )
		// also remove ones that require a data collection that is not turnerned on
		foreach($types as $key=>$value)
		{
			if($value['allowed']===0)
			{
				unset($types[$key]);
			}
			else
			{
				foreach(array('email','table','results','post') as $method)
				{
					if($data_collection[$method]['do']=='')
					{
						if($value[$method]['required']===true)
						{
							unset($types[$key]);
						}
					}
				}
			}
		}
		foreach($fields as $field)
		{
			if(isset($types[$field['type']]))
			{
				$types[$field['type']]['allowed']--;
				if($types[$field['type']]['allowed']===0)
				{
					unset($types[$field['type']]);
				}
			}
		}
		uasort($types,array($this,'types_sort'));
		return $types;
	}
/*****************************************************************************************
* sort types callback
*****************************************************************************************/
	function types_sort($a, $b) {
		if ($a ['text'] == $b ['text']) {
			return 0;
		}
		return ($a ['text'] < $b ['text']) ? - 1 : 1;
	}
	
}		