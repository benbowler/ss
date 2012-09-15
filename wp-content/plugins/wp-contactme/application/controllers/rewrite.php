<?php
/*****************************************************************************************
* url rewriting
*****************************************************************************************/
class contactme_rewrite extends wv48fv_action {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function sandboxWPpageMeta($return) {
		$return ['slug'] = $this->application ()->slug;
		return $return;
	}
	public function sandboxWPpage($subpage) {
		$return = false;
		if (count ( $subpage ) == 2) {
			$forms = $this->form ()->forms ();
			$pi = pathinfo ( $subpage [0] );
			$form = $pi ['filename'];
			$pi = pathinfo ( $subpage [1] );
			$switch = $pi ['filename'];
			$ext = '';
			if (isset ( $pi ['extension'] )) {
				$ext = $pi ['extension'];
			}
			if (isset ( $forms [$form] )) {
				switch ($ext) {
					case 'png' :
						$return = $this->png ( $form, $switch );
						break;
					case 'csv' :
						$return = $this->csv ( $form, $switch );
						break;
				}
			}
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function png($form, $switch) {
		$return = false;
		$polls = $this->form ( $form )->fields ()->poll_details ();
		if (isset ( $polls [$switch - 1] )) {
			$poll = $polls [$switch - 1]->poll_question ['details'];
			$graphics = $this->data ( $form )->graphics;
			$return = true;
			$options = "'".implode("','",$poll['options'])."'";
			// fix to make table safe.
			$question = $poll ['question'];
			$sql = "
SELECT 	`%s`		AS 'option',
		COUNT(*)	AS 'count'
FROM 	`%s`
WHERE 	`%s` in(%s) AND
		(
			`info[submission]` IS NULL OR
			`info[submission]` = 1
		)
GROUP BY `%s`
ORDER BY `%s` DESC;";
			$table = $this->application ()->slug . '_' . $form;
			if(!$this->table ( $table )->exists())
			{
				return $return;
			}
			$sql = sprintf ( $sql, $question, $this->table ( $table )->name (),$question,$options, $question, $question );
			$data = $this->table ()->execute ( $sql );
			$max = 0;
			foreach ( $data as $datum ) {
				$max = $datum ['count'];
				break;
			}
			$options = $poll ['options'];
			$options = array_flip ( $options );
			foreach ( $options as &$option ) {
				$option = 0;
			}
			unset ( $option );
			foreach ( $data as $datum ) {
				$options [$datum ['option']] = $datum ['count'];
			}
			$sum = array_sum ( $options );
			$chart = new contactme_gcharts();
			$name = $poll ['question'];
			$height = ((count ( $options )) * 24) + 60;
			switch ($graphics ['type']) {
				case 'bhs' :
				case 'bvs' :
					$labels = $chart->labels(array_reverse(array_keys ( $options )),1);
					switch ($graphics ['type']) {
						case 'bhs' :
							$chart->bar_chart('h');
							$chart->add_axis('y', $labels,true);
							break;
						case 'bvs' :
							$chart->bar_chart('v');
							$chart->add_axis('x',$labels,true );
							break;
					}
					$labels = $chart->labels("{$sum} Votes",50);
					$range=$chart->range(0,$max);
					$options = $this->percent ( $options, 1, '' );
					$scale=$chart->scale(0,max ( $options ));
					$chart->add_axis('x',$labels,false,$range,$scale);
					$chart->percent();
					break;
				case 'p' :
				case 'p3' :
					$labels = $chart->labels(array_keys ( $options ),1);
					$height=intval($graphics['width']/1.5);
					$legend = $this->percent ( $options );
					switch ($graphics ['type']) {
						case 'p' :
							$chart->pie_chart('2d',$legend,$labels);
							break;
						case 'p3' :
							$chart->pie_chart('3d',$legend,$labels);
							break;
					}
					$name .= ' (' . array_sum ( $options ) . ' Votes)';
					break;
			}
			$chart->title($name); // chart name
			$chart->data($options); // chart data
			$chart->chart_colors($graphics ['colors']);
			$chart->size($graphics ['width'],$height); // chart size
			$chart->show();
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function csv($form, $switch) {
		$return = false;
		$where = '';
		switch ($switch) {
			case 'all' :
				$return = true;
				break;
		}
		if ($return) {
			$this->send_headers ( "{$form}.csv" );
			//$this->send_headers ( ".txt" );
			$sql = "
SELECT	*
FROM	`%s`;
";
			$table = $this->application ()->slug . '_' . $form;
			$sql = sprintf ( $sql, $this->table ( $table )->name () );
			$data = $this->table ()->execute ( $sql );
			if (count ( $data ) > 0) {
				echo '"' . implode ( '","', array_keys ( $data [0] ) ) . '"' . "\n";
			}
			foreach ( $data as $datum ) {
				foreach ( $datum as &$value ) {
					$value = stripslashes ( $value );
					$value = str_replace ( '"', '""', $value );
				}
				unset ( $value );
				echo '"' . implode ( '","', $datum ) . '"' . "\n";
			}
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function percent($array, $scale = 100, $suffix = '%') {
		$sum = array_sum ( $array );
		foreach ( $array as $key => $value ) {
			if ($sum == 0) {
				$array [$key] = '0' . $suffix;
			} else {
				if ($scale == 1) {
					$array [$key] = (($value / $sum) * $scale) . $suffix;
				} else {
					$array [$key] = round ( ($value / $sum) * $scale, 0, PHP_ROUND_HALF_UP ) . $suffix;
				}
			}
		}
		return $array;
	}
/*****************************************************************************************
* ??document??used??
*****************************************************************************************/
	protected function get_path($root = '') {
		$page = strtolower ( $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'] );
		$rootlen = strlen ( str_replace ( 'http://', '', $root ) );
		$page = trim ( substr ( $page, $rootlen ), '/' );
		$pages = explode ( '/', $page );
		return $pages;
	}
}