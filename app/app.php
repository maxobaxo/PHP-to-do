<?php
    date_default_timezone_set('America/Los_Angeles');
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Task.php";
    require_once __DIR__."/../src/Category.php";

    $app = new Silex\Application();

    $server = 'mysql:host=localhost:8889;dbname=to_do';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' =>__DIR__.'/../views'
    ));

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig', array('categories' => Category::getAll(), 'tasks' => Task::getAll()));
    });

    $app->get("/tasks", function() use ($app) {
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });

    $app->get("/categories", function() use ($app) {
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    $app->post("/tasks", function() use ($app) {
        $description = $_POST['description'];
        $date = $_POST['date'];
        $task = new Task($_POST['description'], $_POST['date']);
        $task->save();
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });
    // $app->patch("/tasks", function() use ($app){
    //     $finished = $_POST['checked'];
    //     $tasks = Task::getAll();
    //     $result = array();
    //     foreach($tasks as $task) {
    //         if ($finished == true) {
    //
    //         }
    //     }
    // })

    $app->get("/tasks/{id}", function ($id) use ($app) {
        $task = Task::find($id);
        return $app['twig']->render('task.html.twig', array('task' => $task, 'categories' => $task->getCategories(), 'all_categories' => Category::getAll()));
    });

    $app->get("/tasks/{id}/edit", function($id) use ($app) {
       $task = Task::find($id);
       return $app['twig']->render('task_edit.html.twig', array('task' => $task));
   });

    $app->post("/categories", function() use ($app) {
      $category = new Category($_POST['name']);
      $category->save();
      return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    $app->get("/categories/{id}", function($id) use ($app) {
        $category = Category::find($id);
        return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks(), 'all_tasks' => Task::getAll()));
    });

    $app->post("/add_tasks", function() use($app){
        $category = Category::find($_POST['category_id']);
        $task = Task::find($_POST['task_id']);
        $category->addTask($task);
        return $app['twig']->render('category.html.twig', array('category' => $category, 'categories' =>Category::getAll(), 'tasks' => $category->getTasks(), 'all_tasks' =>Task::getAll()));
    });

    $app->post("/add_categories", function() use ($app) {
      $category = Category::find($_POST['category_id']);
      $task = Task::find($_POST['task_id']);
      $task->addCategory($category);
      return $app['twig']->render('task.html.twig', array('task' => $task, 'tasks' => Task::getAll(), 'categories' => $task->getCategories(), 'all_categories' => Category::getAll()));
    });

    $app->get("/categories/{id}/edit", function($id) use ($app) {
        $category = Category::find($id);
        return $app['twig']->render('category_edit.html.twig', array('category' => $category));
    });

    $app->patch("/categories/{id}", function($id) use ($app) {
        $name = $_POST['name'];
        $category = Category::find($id);
        $category->update($name);
        return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks(), 'all_tasks' => Task::getAll()));
    });

    $app->patch("/tasks/{id}", function($id) use ($app) {
        $finished = $_POST['finished'];
        $task =Task::find($id);
        $task->updateFinished($finished);
        return $app['twig']->render('task.html.twig', array('task' => $task, 'tasks' => $task->getTasks(), 'all_tasks' => Task::getAll()));
    });

    $app->post("/delete_categories", function() use ($app) {
      Category::deleteAll();
      return $app['twig']->render('index.html.twig');
    });

    $app->post("/delete_tasks", function() use ($app) {
        Task::deleteAll();
        return $app['twig']->render('index.html.twig');
    });

    $app->delete("/categories/{id}", function($id) use ($app) {
        $category = Category::find($id);
        $category->delete();
        return $app['twig']->render('index.html.twig', array('categories' => Category::getAll()));
    });

    return $app;
?>
