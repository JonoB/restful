<?php namespace Jonob\Restful;

use Illuminate\Support\Facades\Route;

class Restful
{
    protected $template = array(
        array(
            'method' 	=> 'get',
            'route' 	=> '/{id}/edit',
            'as' 		=> ':singularEdit',
            'uses' 		=> ':resource@edit',
        ),
        array(
            'method' 	=> 'get',
            'route' 	=> '/add',
            'as' 		=> ':singularCreate',
            'uses' 		=> ':resource@create',
        ),
        array(
            'method' 	=> 'get',
            'route' 	=> '/{id}',
            'as' 		=> ':singular',
            'uses' 		=> ':resource@show',
        ),
        array(
            'method' 	=> 'get',
            'route' 	=> '',
            'as' 		=> ':resource',
            'uses' 		=> ':resource@index',
        ),
        array(
            'method' 	=> 'post',
            'route' 	=> '',
            'as'		=> ':singularStore',
            'uses' 		=> ':resource@store',
        ),
        array(
            'method' 	=> 'put',
            'route'		=> '/{id}',
            'as' 		=> ':singularUpdate',
            'uses' 		=> ':resource@update',
        ),
        array(
            'method' 	=> 'delete',
            'route' 	=> '/{id}',
            'as' 		=> ':singularDelete',
            'uses' 		=> ':resource@destroy',
        ),
    );

    /**
     * Create new restful routes
     *
     * @param string $resource   The name of the route pluralized
     * @param string $controller The controller to route to
     * @param array  $template   Optionally pass in a template
     */
    public function __construct($resource, $controller, $template = array())
	{
        $prefix = '';

        // Use the custom template if its been provided
        if ( ! empty($template))
        {
            $this->template = $template;
        }

        // We allow nesting of routes for proper restful representation
        $pieces = explode('.', $resource);
		$resourcePrefix = '';
		$asPrefix = '';
		if (count($pieces) > 1)
		{
			$resource = array_pop($pieces);

			foreach ($pieces as $piece)
			{
				$singularPiece = $this->singular($piece);
				$resourcePrefix .= $piece . '/{' . $singularPiece . '_id}/';
				$asPrefix .= ucfirst($singularPiece);
			}
		}

		$singular = $this->singular($resource);

        // Create each route in the template
        foreach($this->template as $route)
        {
            $method = strtolower($route['method']);
            if ( ! in_array($method, array('get', 'post', 'put', 'delete')))
            {
                throw new \Exception('Invalid method specified:' . $method);
            }
            $options = $this->buildOptions($route, $controller, $resource, $singular, $asPrefix);

            Route::$method($resourcePrefix.$resource.$route['route'], $options);
        }
	}

    /**
     * Create new restful routes
     *
     * @param string $resource   The name of the route pluralized
     * @param string $controller The controller to route to
     * @param array  $template   Optionally pass in a template
     */
	public static function make($resource, $controller, $template = array())
	{
	    return new Restful($resource, $controller, $template);
	}

    /**
     * Build the options for each route
     *
     * @param string $route
     * @return array
     */
    protected function buildOptions($route, $controller, $resource, $singular, $asPrefix)
    {
        $options = array();
        if ( ! empty($route['as']))
		{
            $options['as'] = $asPrefix . $this->replaceTags($route['as'], $resource, $singular);
        }

        if ( ! empty($route['uses']))
		{
			$options['uses'] = $this->replaceTags($route['uses'], $controller, $singular);
        }

        return $options;
    }

    /**
     * Replace tags in the route options
     *
     * @param string $string
     * @return string
     */
    protected function replaceTags($string, $resource, $singular)
    {
        return str_replace(array(':resource', ':singular'), array(ucfirst($resource), ucfirst($singular)), $string);
    }

    /**
     * Return the singular inflection of a word
	 * Copied from Laravel 3
     *
     * @param  string $word
     * @return string
     */
    protected function singular($word)
    {
    	$singular = array(
			'/(quiz)zes$/i' => "$1",
			'/(matr)ices$/i' => "$1ix",
			'/(vert|ind)ices$/i' => "$1ex",
			'/^(ox)en$/i' => "$1",
			'/(alias)es$/i' => "$1",
			'/(octop|vir)i$/i' => "$1us",
			'/(cris|ax|test)es$/i' => "$1is",
			'/(shoe)s$/i' => "$1",
			'/(o)es$/i' => "$1",
			'/(bus)es$/i' => "$1",
			'/([m|l])ice$/i' => "$1ouse",
			'/(x|ch|ss|sh)es$/i' => "$1",
			'/(m)ovies$/i' => "$1ovie",
			'/(s)eries$/i' => "$1eries",
			'/([^aeiouy]|qu)ies$/i' => "$1y",
			'/([lr])ves$/i' => "$1f",
			'/(tive)s$/i' => "$1",
			'/(hive)s$/i' => "$1",
			'/(li|wi|kni)ves$/i' => "$1fe",
			'/(shea|loa|lea|thie)ves$/i' => "$1f",
			'/(^analy)ses$/i' => "$1sis",
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => "$1$2sis",
			'/([ti])a$/i' => "$1um",
			'/(n)ews$/i' => "$1ews",
			'/(h|bl)ouses$/i' => "$1ouse",
			'/(corpse)s$/i' => "$1",
			'/(us)es$/i' => "$1",
			'/(us|ss)$/i' => "$1",
			'/s$/i' => "",
		);

    	$irregular = array(
			'child' => 'children',
			'foot' => 'feet',
			'goose' => 'geese',
			'man' => 'men',
			'move' => 'moves',
			'person' => 'people',
			'sex' => 'sexes',
			'tooth' => 'teeth',
		);

    	$uncountable = array(
			'audio',
			'equipment',
			'deer',
			'fish',
			'gold',
			'information',
			'money',
			'rice',
			'police',
			'series',
			'sheep',
			'species',
			'moose',
		);

		// If the word hasn't been cached, we'll check the list of words that
		// that are "uncountable". This should be a quick look up since we
		// can just hit the array directly for the value.
		if (in_array(strtolower($word), $uncountable))
		{
			return $word;
		}

		// Next, we will check the "irregular" patterns, which contain words
		// like "children" and "teeth" which can not be inflected using the
		// typically used regular expression matching approach.
		foreach ($irregular as $irregular => $pattern)
		{
			if (preg_match($pattern = '/'.$pattern.'$/i', $word))
			{
				return preg_replace($pattern, $irregular, $word);
			}
		}

		// Finally we'll spin through the array of regular expressions and
		// and look for matches for the word. If we find a match we will
		// cache and return the inflected value for quick look up.
		foreach ($singular as $pattern => $inflected)
		{
			if (preg_match($pattern, $word))
			{
				return preg_replace($pattern, $inflected, $word);
			}
		}
    }
}