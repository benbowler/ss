			<ul class="wpsqt_multiple_question">
			<?php foreach ( $question['answers'] as $answerKey => $answer ){ ?>
				<li>
					<input type="<?php echo ($question['type'] == 'Single' ) ? 'radio' : 'checkbox'; ?>" name="answers[<?php echo $questionKey; ?>][]" value="<?php echo $answerKey; ?>" id="answer_<?php echo $question['id']; ?>_<?php echo $answerKey;?>" <?php if ( (isset($answer['default']) && $answer['default'] == 'yes') || in_array($answerKey, $givenAnswer)) {  ?> checked="checked" <?php } ?> /> <label for="answer_<?php echo $question['id']; ?>_<?php echo $answerKey;?>"><?php echo esc_html( $answer['text'] ); ?></label> 
				</li>
			<?php } 
				if (    $question['type'] == 'Multiple Choice' 
					 && array_key_exists('include_other',$question)
					 && $question['include_other'] == 'yes' ){					
				?>
				<li>
					<input type="checkbox" name="answers[<?php echo $questionKey; ?>]" value="0" id="answer_<?php echo $question['id']; ?>_other"> <label for="answer_<?php echo $question['id']; ?>_other">Other</label> <input type="text" name="other[<?php echo $questionKey; ?>]" value="" />
				</li>
				<?php } ?>
			</ul>