<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Bsd\FormBuilder\BsdApi;
use Google\Cloud\Datastore\DatastoreClient;

$app->get('/', function (Request $request) use ($app) {

    /** @var Twig_Environment $twig */
    $twig = $app['twig'];

    return $twig->render('base.html.twig', array());
});

$app->get('/embed', function (Request $request) use ($app) {

    /** @var Twig_Environment $twig */
    $twig = $app['twig'];

    return $twig->render('sidebar.html.twig', array());
});

$app->get('/embed/h', function (Request $request) use ($app) {

    /** @var Twig_Environment $twig */
    $twig = $app['twig'];

    return $twig->render('horizontal.html.twig', array());
});

$app->post('/api/ijqw7dx/signup/{form}', function (Request $request, $form) use ($app) {


    /**
     * Load Config & Initiate BSD Class
     */

    $config = [
        "urlRoot" => $app['config']['bsd_url_root'],
        "appSecret" => $app['config']['bsd_app_secret'],
        "appId" => $app['config']['bsd_app_id'],
    ];

    $bsdApi = new \Bsd\FormBuilder\BsdApi($config);


    /**
     * Place Request Data into format for Processing
     */

    $signupFields = $request->request->all();

    foreach ($signupFields as $key => $value) {
        $signupData[] = [
            "id" => $key,
            "value" => $value,
        ];
    }


    /**
     * Send to BSD API and Return Response
     */

    $signupResponse = $bsdApi->process_signup($form, $signupData);

    if ($signupResponse['http'] == 200) {


    } else {
        // Instantiates a client
        $datastore = new DatastoreClient([
            'projectId' => $app['config']['google_project_id']
        ]);

        // Add New Records
        $failure = $datastore->entity('Failed', [
            'form' => $form,
            'data' => $signupData,
            'reponse' => $signupResponse,
            'date' => date('Y-m-d H:i:s'),
        ]);
        $datastore->insert($failure);
    }

    return $app->json($signupResponse, 200);

});

$app->get('/failures', function (Request $request) use ($app) {

    // Instantiates a client
    $datastore = new DatastoreClient([
        'projectId' => $app['config']['google_project_id']
    ]);

    // Create Query
    $query = $datastore->query()
        ->kind('Failed');

    // Run Query
    $results = $datastore->runQuery($query);

    // Get Result Set
    $failures = [];
    foreach ($results as $entity) {
        $failure = $entity->get();
        $failure['id'] = $entity->key()->pathEndIdentifier();
        $failures[] = $failure;
    }

    return $app->json($failures, 200);
});