<?php

namespace iseeyoucopy\phpmvc;

use iseeyoucopy\phpmvc\db\Database;
use iseeyoucopy\phpmvc\models\Product;

/**
 * Class Application
 *
 * @author  iseeyoucopy <iseeyoucopy@yahoo.com>
 * @package app
 */
class Application
{
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    const EVENT_AFTER_REQUEST = 'afterRequest';

    protected array $eventListeners = [];

    public static Application $app;
    public static string $ROOT_DIR;
    public string $userClass;
    public string $layout = 'main';
    public Router $router;
    public Request $request;
    public Response $response;
    public ?Controller $controller = null;
    public Database $db;
    public Session $session;
    public View $view;
    public ?Product $product;
    public ?UserModel $user;

    /**
     * Constructs a new instance of the class.
     *
     * @param string $rootDir The root directory of the application.
     * @param array $config The configuration array.
     */
    public function __construct($rootDir, $config)
    {
        // Initialize properties
        $this->user = null;
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR = $rootDir;
        self::$app = $this;

        // Initialize request and response objects
        $this->request = new Request();
        $this->response = new Response();

        // Initialize router, database, session, and view objects
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config['db']);
        $this->session = new Session();
        $this->view = new View();

        // Retrieve user from session if user ID exists
        $userId = Application::$app->session->get('user');
        if ($userId) {
            $key = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$key => $userId]);
        }
    }

    /**
     * Check if the user is a guest.
     *
     * @return bool True if the user is a guest, false otherwise.
     */
    public static function isGuest()
    {
        // If the user property of the app object is not set, return true.
        return !self::$app->user;
    }

    /**
     * Logs in a user and sets the user session.
     *
     * @param UserModel $user The user to be logged in.
     * @return bool Returns true if the user is successfully logged in, false otherwise.
     */
    public function login(UserModel $user)
    {
        // Set the user property
        $this->user = $user;

        // Get the class name of the user model
        $className = get_class($user);

        // Get the primary key of the user model
        $primaryKey = $className::primaryKey();

        // Get the value of the primary key
        $value = $user->{$primaryKey};

        // Set the 'user' session key to the value of the primary key
        Application::$app->session->set('user', $value);

        // Return true to indicate successful login
        return true;
    }

    /**
     * Logout the current user.
     */
    public function logout()
    {
        // Set the user property to null
        $this->user = null;

        // Remove the 'user' key from the session
        self::$app->session->remove('user');
    }

    public function run()
    {
        // Trigger the 'before request' event
        $this->triggerEvent(self::EVENT_BEFORE_REQUEST);

        try {
            // Resolve the route and echo the result
            echo $this->router->resolve();
        } catch (\Exception $e) {
            // If an exception is caught, render the '_error' view and echo the result
            echo $this->router->renderView('_error', [
                'exception' => $e,
            ]);
        }
    }

// Refactored code to comply with line length limit of 66 characters
    public function triggerEvent($eventName)
    {
        // Get the array of callbacks for the given event name,
        // or an empty array if it is not set
        $callbacks = $this->eventListeners[$eventName] ?? [];

        // Iterate through each callback
        foreach ($callbacks as $callback) {
            // Call the callback function
            call_user_func($callback);
        }
    }

    /**
     * Register an event listener for a specific event.
     *
     * @param string $eventName The name of the event.
     * @param callable $callback The callback function to be executed when the event is triggered.
     * @return void
     */
    public function on($eventName, $callback)
    {
        // Check if the event name already exists in the event listeners array.
        if (!isset($this->eventListeners[$eventName])) {
            // If it doesn't exist, create a new empty array for the event name.
            $this->eventListeners[$eventName] = [];
        }

        // Add the callback function to the array of event listeners for the specific event.
        $this->eventListeners[$eventName][] = $callback;
    }

}