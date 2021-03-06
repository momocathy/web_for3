<?php defined('SYSPATH') OR die('No direct access.');
/**
 * Math captcha class.
 *
 * @package		Captcha
 * @subpackage	Captcha_Math
 * @author		Michael Lavers
 * @author		Kohana Team
 * @copyright  (c) 2008-2010 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Captcha_Math extends Captcha
{
	/**
	 * @var string Captcha math exercise
	 */
	private $math_exercise;

	/**
	 * Generates a new Captcha challenge.
	 *
	 * @return string The challenge answer
	 */
	public function generate_challenge()
	{
		// Easy
		if (Captcha::$config['complexity'] < 4)
		{
			$numbers[] = mt_rand(1, 5);
			$numbers[] = mt_rand(1, 4);
		}
		// Normal
		elseif (Captcha::$config['complexity'] < 7)
		{
			$numbers[] = mt_rand(10, 20);
			$numbers[] = mt_rand(1, 10);
		}
		// Difficult, well, not really ;)
		else
		{
			$numbers[] = mt_rand(100, 200);
			$numbers[] = mt_rand(10, 20);
			$numbers[] = mt_rand(1, 10);
		}

		// Store the question for output
		$this->math_exercise = implode(' + ', $numbers).' = ';

		// Return the answer
		return array_sum($numbers);
	}

	/**
	 * Outputs the Captcha riddle.
	 *
	 * @param boolean $html HTML output
	 * @return mixed
	 */
/*	public function render($html = TRUE)
	{

		return $this->math_exercise;
	}*/
    /**
     * Outputs the Captcha image.
     *
     * @param boolean $html HTML output
     * @return mixed
     */
    public function render($html = TRUE)
    {
        // Creates $this->image
        $this->image_create(Captcha::$config['background']);

        // Add a random gradient
        if (empty(Captcha::$config['background']))
        {
            $color1 = imagecolorallocate($this->image, mt_rand(200, 255), mt_rand(200, 255), mt_rand(150, 255));
            $color2 = imagecolorallocate($this->image, mt_rand(200, 255), mt_rand(200, 255), mt_rand(150, 255));
            $this->image_gradient($color1, $color2);
        }

        // Add a few random lines
        for ($i = 0, $count = mt_rand(5, Captcha::$config['complexity'] * 4); $i < $count; $i++)
        {
            $color = imagecolorallocatealpha($this->image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(100, 255), mt_rand(50, 120));
            imageline($this->image, mt_rand(0, Captcha::$config['width']), 0, mt_rand(0, Captcha::$config['width']), Captcha::$config['height'], $color);
        }

        // Calculate character font-size and spacing
        $default_size = min(Captcha::$config['width'], Captcha::$config['height'] * 2) / (utf8::strlen($this->response) + 1);
        $spacing = (int) (Captcha::$config['width'] * 0.9 / utf8::strlen($this->response));

        // Draw each Captcha character with varying attributes
        for ($i = 0, $strlen = utf8::strlen($this->response); $i < $strlen; $i++)
        {
            // Use different fonts if available
            $font = Captcha::$config['fontpath'].Captcha::$config['fonts'][array_rand(Captcha::$config['fonts'])];

            // Allocate random color, size and rotation attributes to text
            $color = imagecolorallocate($this->image, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
            $angle = mt_rand(-40, 20);

            // Scale the character size on image height
            $size = $default_size / 10 * mt_rand(8, 12);
            $box = imageftbbox($size, $angle, $font, utf8::substr($this->response, $i, 1));

            // Calculate character starting coordinates
            $x = $spacing / 4 + $i * $spacing;
            $y = Captcha::$config['height'] / 2 + ($box[2] - $box[5]) / 4;

            // Write text character to image
            imagefttext($this->image, $size, $angle, $x, $y, $color, $font, utf8::substr($this->math_exercise, $i, 1));
        }

        // Output
        return $this->image_render($html);
    }

} // End Captcha Math Driver Class