<?php
if ($question['likertscale'] != "Agree/Disagree") {
	$scale = (int) $question['likertscale'];
	for ( $i = 1; $i <= $scale; $i++){ ?>
		<span class="wpsqt_likert_answer wpsqt_likert_answer_<?php echo $i; ?>"><input type="radio" name="answers[<?php echo $questionKey; ?>]" value="<?php echo $i; ?>" <?php if ( in_array($i, $givenAnswer) ) { ?> checked="checked" <?php } ?> id="answer_<?php echo $question['id']; ?>_<?php echo $i; ?>" /> <label for="answer_<?php echo $question['id']; ?>_<?php echo $i; ?>"><?php echo $i; ?></label></span>
	<?php }
} else {
	?>
	<span class="wpsqt_likert_answer"><input type="radio" name="answers[<?php echo $questionKey; ?>]" value="Strongly Disagree" id="answer_<?php echo $question['id']; ?>_stronglydisagree" /> <label for="answer_<?php echo $question['id']; ?>_stronglydisagree">Strongly Disagree</label></span>
	<span class="wpsqt_likert_answer"><input type="radio" name="answers[<?php echo $questionKey; ?>]" value="Disagree" id="answer_<?php echo $question['id']; ?>_disagree" /> <label for="answer_<?php echo $question['id']; ?>_disagree">Disagree</label></span>
	<span class="wpsqt_likert_answer"><input type="radio" name="answers[<?php echo $questionKey; ?>]" value="No Opinion" id="answer_<?php echo $question['id']; ?>_noopinion" /> <label for="answer_<?php echo $question['id']; ?>_noopinion">No Opinion</label></span>
	<span class="wpsqt_likert_answer"><input type="radio" name="answers[<?php echo $questionKey; ?>]" value="Agree" id="answer_<?php echo $question['id']; ?>_agree" /> <label for="answer_<?php echo $question['id']; ?>_agree">Agree</label></span>
	<span class="wpsqt_likert_answer"><input type="radio" name="answers[<?php echo $questionKey; ?>]" value="Strongly Agree" id="answer_<?php echo $question['id']; ?>_stronglyagree" /> <label for="answer_<?php echo $question['id']; ?>_stronglyagree">Strongly Agree</label></span>
<?php } ?>
<br /><br />
