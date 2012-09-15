<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class contactme_application_form_fields_field extends wv48fv_action {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_form = null;
	private $_field = null;
	private $_field_definition = null;
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __construct($application, $settings) {
		parent::__construct ( $application );
		$this->_form = $settings [0];
		$this->_field = $settings [1];
		$this->_field_definition = $this->application ()->data ( $this->_form )->field_definitions [$this->_field];
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function render(&$data,$screen)
	{
		$email = $data->email_details;
		$field = &$this->_field_definition;
		$this->view->field_definition = &$field;
		$general = $this->data ( $this->_form )->general;
		$views = $this->data ( $this->_form )->views;
		$this->view->views = &$views;
		$data_collection = $this->data ( $this->_form )->data_collection;
		$this->view->field_id = "{$this->application()->slug}_{$this->_form}_{$this->view->field_definition['field']}";
		$this->view->field_name = "{$this->view->field_definition['field']}";
		$this->view->readonly='';
		$options='';
		if	(	
				isset($this->view->field_definition['options']) &&
				is_array($this->view->field_definition['options'])
			)
		{
			$options = 'options/';
		}
		$file = $field['type'];
		switch ($field['type'])
		{
			case 'results_header':
				switch($screen)
				{
					case 'results':
						if($data_collection['results']['do']==='checked' && $field['results']['include']==='checked')
						{
							$this->view->field = $this->view->render_string($views['responses'] ['thank_you'] ['phtml'],false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
				}
				break;		
			case 'post_header':
				switch($screen)
				{
					case 'results':
						if($data_collection['post']['do']==='checked' && $field['post']['include']==='checked')
						{
							$this->view->sender=new stdClass();
							$this->view->sender->name = '';
							$this->view->sender->email = '';
							foreach ($email as $email)
							{
								$this->view->sender->name = $email->name['value'];
								$this->view->sender->email = $email->email['value'];
							}
							$data->post[] = $this->view->render_string($field['post']['text'],false);		
						}
						break;
				}
				break;
			case 'email_header':
				switch($screen)
				{
					case 'results':
						if($data_collection['email']['do']==='checked' && $field['email']['include']==='checked')
						{
							$this->view->sender=new stdClass();
							$this->view->sender->name = '';
							$this->view->sender->email = '';
							foreach ($email as $email)
							{
								$this->view->sender->name = $email->name['value'];
								$this->view->sender->email = $email->email['value'];
							}
							$data->email[] = $this->view->render_string($field['email']['text'],false);		
						}
						break;
				}
				break;
			case 'quiz_header':
				switch($screen)
				{
					case 'results':
						$this->view->correct = $field['quiz']['correct'];
						$this->view->questions = $field['quiz']['questions'];
						if($data_collection['results']['do']==='checked' && $field['results']['include']==='checked')
						{
							$this->view->field=$this->view->render_string($field['quiz']['text']).'<hr/>';
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						if($data_collection['email']['do']==='checked' && $field['email']['include']==='checked')
						{
							$data->email[] = $this->view->render_string($field['quiz']['text'])."\n".str_pad('',40,'-');	
						}
						if($data_collection['post']['do']==='checked' && $field['post']['include']==='checked')
						{
							$data->post[] = $this->view->render_string($field['quiz']['text'])."<hr/>";	
						}
						if($data_collection['table']['do']==='checked' && $field['table']['include']==='checked')
						{
							$data->table->data['info[quiz_questions]'] = $field['quiz']['questions'];	
							$data->table->data['info[quiz_score]'] = $field['quiz']['correct'];	
							$data->table->type['info[quiz_questions]'] = 'bigint(20)';	
							$data->table->type['info[quiz_score]'] = 'bigint(20)';	
						}
						break;
				}
				break;
			case 'closed_header':
				switch($screen)
				{
					case 'closed':
						if($field['closed']['include']==='checked')
						{
							$this->view->field = $this->view->render_string($views['responses'] ['closed'] ['phtml'],false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
				}
				break;		
			case 'pending_header':
				switch($screen)
				{
					case 'pending':
						if($field['pending']['include']==='checked')
						{
							$this->view->field = $this->view->render_string($views['responses'] ['pending'] ['phtml'],false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
				}
				break;		
			case 'rss_header':
				switch($screen)
				{
					case 'rss':
						if($field['rss']['include']==='checked')
						{
							$data->rss[] = $this->view->render_string($views['responses'] ['rsserror'] ['phtml'],false);		
						}
						break;
				}
				break;
			case 'cookie':
				$this->view->cookie_name = $this->application ()->slug . '_' . strtolower ( $this->_form ).'_'.$this->user()->user_login ;
				switch($screen)
				{
					case 'cookie':
					case 'cookie':
						if($field['form']['include']==='checked')
						{
							$time = $_COOKIE[$this->view->cookie_name];
							$time = ($general ['duration'] * $general ['scale'])-(time()-$time);
							if($time>(365*24*60*60))
							{
								$time = intval($time/(365*24*60*60)).' years';
							}
							elseif($time>(7*24*60*60))
							{
								$time = intval($time/(7*24*60*60)).' weeks';
							}
							elseif($time>(24*60*60))
							{
								$time = intval($time/(24*60*60)).' days';
							}
							elseif($time>(60*60))
							{
								$time = intval($time/(60*60)).' hours';
							}
							elseif($time>60)
							{
								$time = intval($time/60).' minutes';
							}
							else
							{
								$time = intval($time).' seconds';
							}
							$this->view->time_left = " {$time} ";
							$views['responses'] ['already_submitted'] ['phtml'] = $this->view->render_string($views['responses'] ['already_submitted'] ['phtml'],false);	
							$filename = "blog/cookie.phtml";
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'results':
						if($data_collection['results']['do']==='checked' && $field['results']['include']==='checked')
						{
							$this->view->cookie_expire = date ( 'D, d M Y H:i:0', time () + ($general ['duration'] * $general ['scale']) ) . ' UTC'; //+ (60 * 60 * 24 * 365 * 5) );
							$filename = "results/{$file}.phtml";
							$data->front[] = $this->render_script($filename,false);	
						}
						break;
				}
				break;		
			case 'wpuser':
				switch($screen)
				{
					case 'open':
					case 'error':
						if($data_collection['form']['do']==='checked' && $field['form']['include']==='checked')
						{
							$file = 'hidden';
							$field['value'] = $this->user ()->ID;
							$filename = "blog/{$options}{$file}.phtml";
							$data->front[] = $this->render_script($filename,false);	
						}
						break;
					case 'results':
						$user = 'Not logged in.';
						$field['question']='WordPress User';
						$value = $field['value'];
						if($field['value']>0)
						{
							$user = $this->user($field['value'])->display_name;
						}
						$field ['value'] = $user;
						if($data_collection['results']['do']==='checked' && $field['results']['include']==='checked')
						{
							$filename = "results/default.phtml";
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						if($data_collection['email']['do']==='checked' && $field['email']['include']==='checked')
						{
							$filename = "email_txt/default.phtml";
							$data->email[] = $this->render_script($filename,false);	
						}
						if($data_collection['post']['do']==='checked' && $field['post']['include']==='checked')
						{
							$filename = "post/default.phtml";
							$data->post[] = $this->render_script($filename,false);	
						}
						if($data_collection['table']['do']==='checked' && $field['table']['include']==='checked')
						{
							$data->table->data['info[wp_user]'] = $value;	
							$data->table->type['info[wp_user]'] = 'bigint(20)';	
						}
					break;
				}
				break;
			case 'textstring':
				$filename = "blog/{$options}{$file}.phtml";
				switch($screen)
				{
					case 'open':
					case 'error':
						if($data_collection['form']['do']==='checked' && $field['form']['include']==='checked')
						{
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'private':
					case 'cookie':
						if($field[$screen]['include']==='checked')
						{
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'pending':
					case 'closed':
						if($field[$screen]['include']==='checked')
						{
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'results':
						if($data_collection['results']['do']==='checked' && $field['results']['include']==='checked')
						{
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						if($data_collection['email']['do']==='checked' && $field['email']['include']==='checked')
						{
							$data->email[] = $field['question_long'];	
						}
						if($data_collection['post']['do']==='checked' && $field['post']['include']==='checked')
						{
							$data->post[] = $field['question_long'];	
						}
						break;
					case 'rss':
						if($field['rss']['include']==='checked')
						{
							$data->rss[] = $field['question_long'];	
						}
						break;
				}
				break;
			case 'cc':
			case 'checkbox':
				$file='checkbox';
				$filename = "blog/{$options}{$file}.phtml";
				switch($screen)
				{
					case 'open':
					case 'error':
						if($data_collection['form']['do']==='checked' && $field['form']['include']==='checked')
						{
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'private':
					case 'cookie':
						if($field[$screen]['include']==='checked')
						{
							$this->view->readonly=' disabled ';
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'results':
						$value = $field['value'];
						if(is_array($field['value']))
						{
							$field['value'] = implode(', ',$this->view->field_definition['value']);
						}
						else
						{
							if($field['value']=="1")
							{
								$field['value'] = 'yes';
							}
							else
							{
								$field['value'] = 'no';
							}
						}
						if($data_collection['results']['do']==='checked' && $field['results']['include']==='checked')
						{
							$filename = "results/default.phtml";
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						if($data_collection['email']['do']==='checked' && $field['email']['include']==='checked')
						{
							$filename = "email_txt/default.phtml";
							$data->email[] = $this->render_script($filename,false);		
						}
						if($data_collection['post']['do']==='checked' && $field['post']['include']==='checked')
						{
							$filename = "post/default.phtml";
							$data->post[] = $this->render_script($filename,false);		
						}
						if($data_collection['table']['do']==='checked' && $field['table']['include']==='checked')
						{
							if (is_array ( $field ['options'] )) {
								$question=$field['question'];
								$this->table()->field_name($question,30);
								foreach ( $field ['options'] as $opt ) {
									$val = 0;
									if (in_array ( $opt, ( array ) $value )) {
										$val = 1;
									}
									$this->table()->field_name($opt,30);
									$data->table->data["{$question}[{$opt}]"] = $val;
									$data->table->type["{$question}[{$opt}]"] = 'text';
								}
							} else {
								$question=$field['question'];
								$this->table()->field_name($question);
								$data->table->data[$question] = $value;
								$data->table->type[$question] = 'text';
							}
						}
						break;
				}
				break;
			case 'radio':
			case 'quiz_question':
			case 'poll_question':
				$file='radio';
				$filename = "blog/{$options}{$file}.phtml";
				$value = $field['value'];
				switch($screen)
				{
					case 'open':
					case 'error':
						if($data_collection['form']['do']==='checked' && $field['form']['include']==='checked')
						{
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'private':
					case 'cookie':
						if($field[$screen]['include']==='checked')
						{
							if($field['type']==='poll_question')
							{
									$filename = "results/poll_question.phtml";
									$field['value'] = $this->control_url ( $this->application ()->slug . "/{$this->_form}/1.png" );
									$this->view->field = $this->render_script($filename,false);
									$data->front[] = $this->render_script('blog/field.phtml',false);	
							}
							else
							{
								$this->view->readonly = ' disabled ';
								$this->view->field = $this->render_script($filename,false);	
								$data->front[] = $this->render_script('blog/field.phtml',false);	
							}
						}
						break;
					case 'pending':
					case 'closed':
						if($field['type']==='poll_question' && $field[$screen]['include']==='checked')
						{
							$filename = "results/poll_question.phtml";
							$this->view->field_definition['value'] = $this->control_url ( $this->application ()->slug . "/{$this->_form}/1.png" );
							$this->view->field = $this->render_script($filename,false);
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'results':
						if($data_collection['results']['do']==='checked' && $field['results']['include']==='checked')
						{
							switch($field['type'])
							{
								case 'radio':
									if($field['value']=="1")
									{
										$field['value'] = 'yes';
									}
									elseif($field['value']=="0")
									{
										$field['value'] = 'no';
									}
									$filename = "results/default.phtml";
									$this->view->field = $this->render_script($filename,false);	
									$data->front[] = $this->render_script('blog/field.phtml',false);	
									break;
								case 'quiz_question':
									$filename = "results/quiz_question/correct.phtml";
									if($field ['value'] != $field ['answer'])
									{	
										$filename = "results/quiz_question/wrong.phtml";
									}
									$this->view->field = $this->render_script($filename,false);
									$data->front[] = $this->render_script('blog/field.phtml',false);	
									break;
								case 'poll_question':
									$filename = "results/poll_question.phtml";
									$field['value'] = $this->control_url ( $this->application ()->slug . "/{$this->_form}/1.png" );
									$this->view->field = $this->render_script($filename,false);
									$data->front[] = $this->render_script('blog/field.phtml',false);	
									break;
							}
						}
						if($data_collection['email']['do']==='checked' && $field['email']['include']==='checked')
						{
							switch($field['type'])
							{
								case 'radio':
									if($field['value']=="1")
									{
										$field['value'] = 'yes';
									}
									elseif($field['value']=="0")
									{
										$field['value'] = 'no';
									}
									$filename = "email_txt/default.phtml";
									$data->email[] = $this->render_script($filename,false);	
									break;
								case 'quiz_question':
									$filename = "email_txt/quiz_question/correct.phtml";
									if($field ['value'] != $field ['answer'])
									{	
										$filename = "email_txt/quiz_question/wrong.phtml";
									}
									$data->email[] = $this->render_script($filename,false);	
									break;
								case 'poll_question':
									$filename = "email_txt/default.phtml";
									$field['value'] = $this->control_url ( $this->application ()->slug . "/{$this->_form}/1.png" );
									$data->email[] = $this->render_script($filename,false);	
									break;
							}
						}
						if($data_collection['post']['do']==='checked' && $field['post']['include']==='checked')
						{
							switch($field['type'])
							{
								case 'radio':
									if($field['value']=="1")
									{
										$field['value'] = 'yes';
									}
									elseif($field['value']=="0")
									{
										$field['value'] = 'no';
									}
									$filename = "post/default.phtml";
									$data->post[] = $this->render_script($filename,false);	
									break;
								case 'quiz_question':
									$filename = "email/quiz_question/correct.phtml";
									if($field ['value'] != $field ['answer'])
									{	
										$filename = "post/quiz_question/wrong.phtml";
									}
									$data->post[] = $this->render_script($filename,false);	
									break;
								case 'poll_question':
									$filename = "results/poll_question.phtml";
									$field['value'] = $this->control_url ( $this->application ()->slug . "/{$this->_form}/1.png" );
									$data->post[] = $this->render_script($filename,false);	
									break;
							}
						}
						if($data_collection['table']['do']==='checked' && $field['table']['include']==='checked')
						{
							$question=$field['question'];
							$this->table()->field_name($question);
							$data->table->data[$question] = $value;
							$data->table->type[$question] = 'text';
						}
						break;
				}
				break;
			case 'text':
			case 'email' :
			case 'name' :
				$file = 'text';
				$filename = "blog/{$options}{$file}.phtml";
				$value = $field['value'];
				switch($screen)
				{
					case 'open':
					case 'error':
						if($data_collection['form']['do']==='checked' && $field['form']['include']==='checked')
						{
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'private':
					case 'cookie':
						if($field[$screen]['include']==='checked')
						{
							$this->view->readonly=' disabled ';
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'results':
						if($data_collection['results']['do']==='checked' && $field['results']['include']==='checked')
						{
							$filename = "results/default.phtml";
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						if($data_collection['email']['do']==='checked' && $field['email']['include']==='checked')
						{
							$filename = "email_txt/default.phtml";
							$data->email[] = $this->render_script($filename,false);
						}
						if($data_collection['post']['do']==='checked' && $field['post']['include']==='checked')
						{
							$filename = "post/default.phtml";
							$data->post[] = $this->render_script($filename,false);
						}
						if($data_collection['table']['do']==='checked' && $field['table']['include']==='checked')
						{
							$question=$field['question'];
							$this->table()->field_name($question);
							$data->table->data[$question] = $value;
							$data->table->type[$question] = 'text';
						}
						break;
				}
				break;
			case 'textarea':
				$filename = "blog/{$options}{$file}.phtml";
				$value = $field['value'];
				switch($screen)
				{
					case 'open':
					case 'error':
						if($data_collection['form']['do']==='checked' && $field['form']['include']==='checked')
						{
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'private':
					case 'cookie':
						if($field[$screen]['include']==='checked')
						{
							$this->view->readonly=' disabled ';
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'results':
						if($data_collection['results']['do']==='checked' && $field['results']['include']==='checked')
						{
							$filename = "results/default.phtml";
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						if($data_collection['email']['do']==='checked' && $field['email']['include']==='checked')
						{
							$filename = "email_txt/default.phtml";
							$data->email[] = $this->render_script($filename,false);	
						}
						if($data_collection['post']['do']==='checked' && $field['post']['include']==='checked')
						{
							$filename = "post/default.phtml";
							$data->post[] = $this->render_script($filename,false);	
						}
						if($data_collection['table']['do']==='checked' && $field['table']['include']==='checked')
						{
							$question=$field['question'];
							$this->table()->field_name($question);
							$data->table->data[$question] = $value;
							$data->table->type[$question] = 'text';
						}
						break;
				}
				break;
			case 'link':
				if(!isset($field['value']['url']))
				{
					$field['value']['url'] = '';
				}
				if(!isset($field['value']['desc']))
				{
					$field['value']['desc'] = '';
				}
				$filename = "blog/{$options}{$file}.phtml";
				switch($screen)
				{
					case 'open':
					case 'error':
						if($data_collection['form']['do']==='checked' && $field['form']['include']==='checked')
						{
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'private':
					case 'cookie':
						if($field[$screen]['include']==='checked')
						{
							$this->view->readonly = ' disabled ';
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'results':
						if($data_collection['results']['do']==='checked' && $field['results']['include']==='checked')
						{
							$filename = "results/link.phtml";
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						if($data_collection['email']['do']==='checked' && $field['email']['include']==='checked')
						{
							$filename = "email_txt/link.phtml";
							$data->email[] = $this->render_script($filename,false);		
						}
						if($data_collection['post']['do']==='checked' && $field['post']['include']==='checked')
						{
							$filename = "post/link.phtml";
							$data->post[] = $this->render_script($filename,false);		
						}
						if($data_collection['table']['do']==='checked' && $field['table']['include']==='checked')
						{
							$question=$field['question'];
							$this->table()->field_name($question,50);
							$data->table->data["{$question}[desc]"] = $field['value']['desc'];
							$data->table->data["{$question}[url]"] = $field['value']['url'];
							$data->table->type["{$question}[desc]"] = 'text';
							$data->table->type["{$question}[url]"] = 'text';
						}
						break;
				}
				break;
			case 'number_captcha':
				switch($screen)
				{
					case 'open':
					case 'error':
						// legacy
						$question = explode('%d',$field['question']);
						if(count($question)===3)
						{
							$field['question']= $question[0].'<?php echo $this->number1;?>'.$question[1].'<?php echo $this->number2;?>'.$question[2];
						}
						$this->view->number1 = $field['captcha']->num1;
						$this->view->number2 = $field['captcha']->num2;
						$field['question']=$this->view->render_string($field['question']);
						if($data_collection['form']['do']==='checked' && $field['form']['include']==='checked')
						{
							$filename = "blog/{$options}{$file}.phtml";
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
				}
				break;
			case 'recaptcha':
				switch($screen)
				{
					case 'open':
					case 'error':
						if($data_collection['form']['do']==='checked' && $field['form']['include']==='checked')
						{
							$recaptcha = new contactme_recaptcha ( $this->application () );
							$data->front[] = $recaptcha->get_html ();	
						}
						break;
				}
				break;
			case 'formsent':
				switch($screen)
				{
					case 'open':
					case 'error':
						// legacy
						if($data_collection['form']['do']==='checked' && $field['form']['include']==='checked')
						{
							$file = 'hidden';
							//move this to the read filter
							$this->view->field_definition['value'] = $this->table ()->date ();
							$filename = "blog/{$options}{$file}.phtml";
							$data->front[] = $this->render_script($filename,false);	
						}
						break;
					case 'results':
						$value = $field['value'];
						$field['question']='Form Sent';
						$field['value']=date(get_option('date_format').' '.get_option('time_format'),strtotime($field ['value']));
						if($data_collection['results']['do']==='checked' && $field['results']['include']==='checked')
						{
							$filename = "results/default.phtml";
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						if($data_collection['email']['do']==='checked' && $field['email']['include']==='checked')
						{
							$filename = "email_txt/default.phtml";
							$data->email[] = $this->render_script($filename,false);	
						}
						if($data_collection['post']['do']==='checked' && $field['post']['include']==='checked')
						{
							$filename = "post/default.phtml";
							$data->post[] = $this->render_script($filename,false);	
						}
						if($data_collection['table']['do']==='checked' && $field['table']['include']==='checked')
						{
							$data->table->data['info[form_recieved]'] = $value;
							$data->table->type['info[form_recieved]'] = 'datetime';
						}
						break;
				}
				break;
			case 'submit':
				switch($screen)
				{
					case 'open':
					case 'error':
						// legacy
						$this->view->field_name = "submit[{$this->application()->slug}][{$this->_form}]";
						if($data_collection['form']['do']==='checked' && $field['form']['include']==='checked')
						{
							$filename = "blog/{$options}{$file}.phtml";
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
					case 'results':
						$field['question']='Form Submitted';
						$field['value']=date(get_option('date_format').' '.get_option('time_format'));
						if($data_collection['results']['do']==='checked' && $field['results']['include']==='checked')
						{
							$filename = "results/default.phtml";
							$this->view->field = $this->render_script($filename,false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						if($data_collection['email']['do']==='checked' && $field['email']['include']==='checked')
						{
							$filename = "email_txt/default.phtml";
							$data->email[] =  $this->render_script($filename,false);	
						}
						if($data_collection['post']['do']==='checked' && $field['post']['include']==='checked')
						{
							$filename = "post/default.phtml";
							$data->post[] =  $this->render_script($filename,false);	
						}
						if($data_collection['table']['do']==='checked' && $field['table']['include']==='checked')
						{
							$data->table->data['info[form_recieved]'] = $this->table()->date();
							$data->table->type['info[form_recieved]'] = 'datetime';
						}
						break;
				}
				break;
			case 'private_footer':
				switch($screen)
				{
					case 'open':
					case 'error':
						break;
					case 'private':
						if($field['private']['include']==='checked')
						{
							$this->view->field = $this->view->render_string($views['responses']['private']['phtml'],false);	
							$data->front[] = $this->render_script('blog/field.phtml',false);	
						}
						break;
				}
				break;
			case 'email_footer':
				switch($screen)
				{
					case 'results':
						if($data_collection['email']['do']==='checked' && $field['email']['include']==='checked')
						{
							$data->email[] = $this->view->render_string($field['email']['text'],false);		
						}
						break;
				}
				break;
			case 'new':
				break;
			default:
				throw new Exception($this->_field_definition['type'].' Not handled.');
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function get_questions($questions = array()) {
		$return = array ();
		$max = 0;
		foreach ( $questions as $question ) {
			$return [$question] = array ();
			foreach ( $this->data ()->field_definitions as $key => $value ) {
				if ($value ['type']=='quiz_question') {
					$return [$question] [] = $value ['value'];
				}
			}
			$max = max ( $max, count ( $return [$question] ) );
		}
		foreach ( $questions as $question ) {
			$return [$question] = array_pad ( $return [$question], $max, '' );
		}
		$data = array ();
		for($cnt = 0; $cnt < $max; $cnt ++) {
			$data [$cnt] = new stdClass ();
			foreach ( $questions as $question ) {
				$data [$cnt]->$question = $return [$question] [$cnt];
			}
		}
		return $data;
	}
}