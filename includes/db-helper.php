<?php
namespace UCF\CAH;

require_once '_mailauth.php';

/**
 * Class to handle database connections, and save on some
 * boilerplate code.
 * 
 * @author Mike W. Leavitt
 * @version 1.0.0
 * 
 * TODO: Make the constructor and connect() function take the database
 * arguments, so this class can be independent of an external file (and
 * can therefore be used anywhere without modification)
 */
if( !class_exists( 'DB_Helper' ) ) {
    class DB_Helper
    {
        // Will hold the mysqli connection object.
        private $db_connection;

        /**
         * Constructor. Automatically connects with the authorization
         * info we required above.
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         * 
         * @return void
         */
        public function __construct() {
            $this->connect();
        }


        /**
         * Destructor. Closes the DB connection if we haven't already.
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         * 
         * @return void
         */
        public function __destruct() {
            $this->close_db();
        }


        /**
         * Returns the database connection, and creates one first if it
         * doesn't exist.
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         * 
         * @return mysqli MySQL connection object.
         */
        public function get_db() {
            if( is_null( $this->db_connection ) ) {
                $this->connect();
            }
            return $this->db_connection;
        }


        /**
         * Closes the database connection when we're done with it.
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         * 
         * @return void
         */
        public function close_db() {
            if( !is_null( $this->db_connection ) ) {
                $this->close();
                $this->db_connection = null;
            }
        }


        /**
         * Connects to a database with the authorization information we
         * required, above.
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         * 
         * @return void
         */
        private function connect() {
            global $db_server, $db_user, $db_pass, $db, $db_charset;

            $this->db_connection = mysqli_connect( $db_server, $db_user, $db_pass, $db );
            mysqli_set_charset( $this->db_connection, $db_charset );
        }


        /**
         * Closes the database connection.
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         * 
         * @return void
         */
        private function close() {
            mysqli_close( $this->db_connection );
        }
    }
}
?>