<?php
/**

*
* ДАННЫЙ КОД ЯВЛЯЕТСЯ НЕДОРАБОТКОЙ, НО МОЖЕТ ФУНКЦИОНИРОВАТЬ. 
* 	Этот набросок требует доработки. Следует отделить общие функции работы с изображениями и оставить
* 	только тот функционал, что отвечает за генерацию линейного графика.
*

**/
class X_Graphs_Linear
{
	private $WIDTH;
	private $HEIGHT;
	private $image;
	private $colors = [];

	private $params = 
	[ 
		"polygon" => 
		[
			"top" => 10,
			"left" => 10,
			"right" => 60,
			"bottom" => 30
		]
	];

	function __construct( $WIDTH, $HEIGHT )
	{
		$this->image = imagecreate( $WIDTH, $HEIGHT );
		$this->WIDTH = $WIDTH;
		$this->HEIGHT = $HEIGHT;

		$this->set_color( "background", 0, 		0, 		0 );
		$this->set_color( "line1", 		79, 	79, 	79 );
		$this->set_color( "line2", 		50, 	50, 	50 );
		$this->set_color( "line3", 		20, 	20, 	20 );
		$this->set_color( 1, 			255, 	0, 		0 );
		$this->set_color( 2, 			0, 		255, 	0 );
		$this->set_color( 3, 			0, 		0, 		255 );
		$this->set_color( 4, 			100, 	100, 	100 );
		$this->set_color( 5, 			200, 	200, 	200 );
		$this->set_color( 6, 			255, 	255, 	255 );

		$this->set_line_width(1);
		$this->set_background();

		$this->draw_polygon();
	}




	public function drawData($xLine, $data)
	{
		$p_WIDTH  = $this->params["polygon"]["WIDTH"];
		$p_HEIGHT = $this->params["polygon"]["HEIGHT"];
		$X1 = $this->params["polygon"]["X1"];
		$Y1 = $this->params["polygon"]["Y1"];
		$X2 = $this->params["polygon"]["X2"];
		$Y2 = $this->params["polygon"]["Y2"];
		list( $min_value, $max_value ) = $this->get_min_max_from_data($data);

		$x_count = count($xLine)-1;
		$x_data_names = [];
		$IDX = 0;
		foreach ($xLine as $key => $value) 
		{
			$X = ($x_count>0)?(( $p_WIDTH * ($IDX) / $x_count ) + $X1):0;
			$x_data_names[$key] = $X;
			imagestring ( $this->image, 1, $X, $Y2 + 2, $value, $this->colors[5] );
			$IDX++;
		}

		$tmv = $max_value - $min_value;
		for ($C=($Y1-7); $C < ($Y2-7); $C+=21) 
		{ 
			$cur = round( $tmv - ( $tmv * ($C-($Y1-7)) / (($Y2-7) - ($Y1-7)) ) +  $min_value, 0 ) ;
			imageline   ( $this->image, $X2+1, $C+7, $X2 + 2, $C+7, $this->colors[5] ); 
			imagestring ( $this->image, 1, $X2 + 4, $C, $cur, $this->colors[5] );
		}

		$color_id = 1;
		$text_pos_x = $X1;
		$text_pos_y = $Y2 + 10;
		foreach ($data as $value_mas) 
		{
			$pin_x = $pin_y = false;
			if(!isset($this->colors[$color_id])) $color_id = 1;
			$color = $this->colors[$color_id]; 
			$color_id++;

			if( isset($value_mas[1] ))
			{
				$t_size = strlen($value_mas[1])*5;
				if(($t_size + $text_pos_x) > $p_WIDTH) { $text_pos_y += 10; $text_pos_x = $X1; }
				imagestring ( $this->image, 1, $text_pos_x, $text_pos_y, $value_mas[1], $color );
				$text_pos_x = $t_size + $text_pos_x + 10;
			}
			foreach ($value_mas[0] as $key => $value) 
			{
				//$tx = (				( $p_WIDTH  * ($key)                / $x_count 	) + $X1);
				$tx = $x_data_names[$key];
				$ty = ( $p_HEIGHT -	( $p_HEIGHT * ($value-$min_value)   / $tmv 		) + $Y1);
				if($pin_x  === false && $pin_y === false)
				{
					$pin_x = $tx;
					$pin_y = $ty;
				}
				else
				{
					imageline ( $this->image, $pin_x, $pin_y, $tx, $ty, $color ); 
					$pin_x = $tx;
					$pin_y = $ty;
				}
			}
		}
	}

	private function get_min_max_from_data($data)
	{
		$min = false;
		$max = 0;
		foreach ($data as $value_mas) 
		{
			foreach ($value_mas[0] as $value) 
			{
				if($value > $max) $max = $value;
				if($value < $min || $min === false ) $min = $value;
			}
		}
		$max += round( $max * 0.1 );
		$min -= round( $min * 0.1 );

		return [ ( ($min<0)?0:$min ), $max ];
	}

	public function draw_polygon()
	{
		$this->params["polygon"]["WIDTH"]  = $this->WIDTH  - ( $this->params["polygon"]["left"] + $this->params["polygon"]["right"] );
		$this->params["polygon"]["HEIGHT"] = $this->HEIGHT - ( $this->params["polygon"]["top"] + $this->params["polygon"]["bottom"] );
		$this->params["polygon"]["X1"] = $X1 = $this->params["polygon"]["left"];
		$this->params["polygon"]["Y1"] = $Y1 = $this->params["polygon"]["top"];
		$this->params["polygon"]["X2"] = $X2 = $this->WIDTH  - $this->params["polygon"]["right"];
		$this->params["polygon"]["Y2"] = $Y2 = $this->HEIGHT - $this->params["polygon"]["bottom"];
		$this->draw_grid();
		imagerectangle( $this->image, $X1, $Y1, $X2, $Y2 , $this->colors['line1'] );
	}

	private function draw_grid()
	{
		$X1 = $this->params["polygon"]["X1"];
		$Y1 = $this->params["polygon"]["Y1"];
		$X2 = $this->params["polygon"]["X2"];
		$Y2 = $this->params["polygon"]["Y2"];

		for ($X = $X2; $X >= $X1; $X -= 15) 
		{ 
			imagesetstyle ($this->image, [ $this->colors['line3'], $this->colors['background'] ]);
			imageline ($this->image, $X, $Y1, $X, $Y2, IMG_COLOR_STYLED); 
		}
		for ($Y = $Y2; $Y >= $Y1; $Y -= 15) 
		{ 
			imagesetstyle ($this->image, [ $this->colors['line3'], $this->colors['background'] ]);
			imageline ($this->image, $X1, $Y, $X2, $Y, IMG_COLOR_STYLED); 
		}

		for ($X = $X2; $X >= $X1; $X -= 60) 
		{ 
			imagesetstyle ($this->image, [ $this->colors['line2'], $this->colors['background'] ]);
			imageline ($this->image, $X, $Y1, $X, $Y2, IMG_COLOR_STYLED); 
		}
		for ($Y = $Y2; $Y >= $Y1; $Y -= 60) 
		{ 
			imagesetstyle ($this->image, [ $this->colors['line2'], $this->colors['background'] ]);
			imageline ($this->image, $X1, $Y, $X2, $Y, IMG_COLOR_STYLED); 
		}
	}

	// not use
	private function draw_line()
	{
		imagesetstyle ($this->image, [ $this->colors['line2'], $this->colors['background'] ]);
		imageline ($this->image, $X, $Y1, $X, $Y2, IMG_COLOR_STYLED); 

	}


	public function draw_legend()
	{
		
	}







	public function set_color( $name, $R, $G, $B )
	{
		$this->colors[$name] = imageColorAllocate( $this->image, $R, $G, $B );
	}
	public function set_line_width( $width )
	{
		imagesetthickness($this->image, $width);
	}

	public function set_background($R=0,$G=0,$B=0)
	{
		if( $R == 0 && $G == 0 && $B == 0 ) $color = $this->colors['background'];
		else $this->colors['background'] = $color = imageColorAllocate( $this->image, $R, $G, $B );
		imageFilledRectangle($this->image, 0, 0, imageSX($this->image), imageSY($this->image), $color);
	}

	public function Display()
	{
		header("Content-Type: image/png");
		imagepng($this->image);
		imagedestroy($this->image);

		
		exit;
	}
}
?>