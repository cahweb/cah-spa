<?php
namespace UCF\CAH\SPA\Theatre\ProgramReqs;

class ProgramReqsSetup
{
    private function __construct() {} // prevents instantiation

    private static $_handle = "program-reqs";

    public static function setup()
    {
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_scripts' ], 5, 0 );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'maybe_load_scripts' ], 10, 0 );

        add_shortcode( self::$_handle, [ __CLASS__, 'shortcode' ] );
    }


    public static function register_scripts()
    {
        $handle = self::$_handle;
        $uri = get_stylesheet_directory_uri() . "/audition-forms/theatre-audition-placeholder/dist";
        $path = get_stylesheet_directory() . "/audition-forms/theatre-audition-placeholder/dist";

        wp_register_script(
            "$handle-chunk",
            "$uri/js/chunk-$handle.js",
            [],
            filemtime( "$path/js/chunk-$handle.js" ),
            true
        );

        wp_register_script(
            "$handle",
            "$uri/js/$handle.js",
            [ "$handle-chunk" ],
            filemtime( "$path/js/$handle.js" ),
            true
        );

        if( file_exists( "$path/css/$handle.css" ) )
        {
            wp_register_style(
                "$handle-style",
                "$uri/css/$handle.css",
                [],
                filemtime( "$path/css/$handle.css" ),
                'all'
            );
        }
    }


    public static function maybe_load_scripts()
    {
        $handle = self::$_handle;

        global $post;

        $parent_slug = get_post( $post->post_parent )->post_name;

        if( 'program-requirements' === $post->post_name && ( 'theatre' === $parent_slug || 'music' === $parent_slug) )
        {
            wp_enqueue_script( "$handle" );
            wp_localize_script( "$handle", "wpVars", [
                'programReqsUri' => get_stylesheet_directory_uri() . "/dist/json",
            ] );

            wp_enqueue_style( "$handle-style" );
        }
    }


    public static function shortcode( $atts = [] )
    {
        $a = shortcode_atts( [
            'spec' => 'theatre',
        ], $atts );

        ob_start();
        ?>
        <input type="hidden" id="spec" value="<?= $a['spec'] ?>">
        <div id="<?= self::$_handle ?>-app"></div>

        <?php
        echo ob_get_clean();
    }
}
?>