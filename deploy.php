<?php

  require 'recipe/wordpress.php';
  // Just using the default wordpress receipie for now, but would need others for craftcms, expressionengine, wordpress
  //
  // For serverpilot, the current release needs to be called "public" instead of "current", so:
  // app_name-stage
  //  - public
  //  - releases
  //  -- 23498234082
  //  -- 23423423421
  // ... etc


  serverList('deploy-servers.yml');
  // EDIT SITE DETAILS
  env('app_name', 'mysite'); // App name is required and can only have lowercase letters, numbers, and hyphens. The first and last characters must be letters or numbers.The minimum length is 3 characters.
  env('app_domain', 'mysite.com.au');
  env('db_user', '');
  env('db_name', '');
  env('db_pass', '');
//  set('repository', 'git@bitbucket.org:pitchstarter/princepropagation.com.au.git');
  set('repository', 'ganzer428@github.com:ganzer428/deployer.git');
// You'll need to create a test repo to work with


  // OTHER OPTIONS - do not change unless required
  env('sp_id', 'cid_q5C04vwJCdWdZNBa'); //serverpilot api id
  env('sp_key', '6zZEGvBAzm7tbLd6nwmBe62ASHQPEkMST2jL9YzGXlQ'); //serverpilot api key
  env('sp_user', 'adLt312w8m9LaMV8'); //serverpilot user
  set('default_stage', 'prod');
  set('keep_releases', 5);
  set('php_version', '7.0'); //5.4, 5.5, 5.6, or 7.0.

  // TASK - Create App in ServerPilot.io
  task('create', function () {

      // create an App
      runLocally('curl https://api.serverpilot.io/v1/apps \
         -u {{sp_id}}:{{sp_key}} \
         -H "Content-Type: application/json" \
         -d \'{"name": "{{app_name}}", "sysuserid": "{{sp_user}}", "runtime": "php{{php_version}}", "domains": ["{{app_domain}}", "www.{{app_domain}}"]}\'
      ');

      //remove the /public directory serverpilot just created
      run('rm -rf public/');

      //
      // TODO: Create a database via serverpilot API
      // see: https://github.com/ServerPilot/API#create-a-database
      // Note: - you'll need to take the json output of the create app api call above to set the app id
      //
      // - using the database details {{db_user}}, {{db_name}} + {{db_pass}} if set above.
      // - If not set, use {{app_name}} = for db user & name and generate a random password.
      // - Echo out the database password so the developer can add the proper config to the CMS
      //

      // TODO: We'll need to have two 'create' tasks. One for staging and one for production.
      //
      // staging directory = {{app_name}}-stage
      // production directory = {{app_name}}-prod

  });

  // TODO: TASK - Import DB
  task('importdb', function () {
    // - Ask for confirmation before continuing
    // - import database dump from /db/latest.sql to stage/prod
    //
  });


  task('reload:php-fpm', function () {
      run('sudo /usr/sbin/service php{{php_version}}-fpm reload');
  });
  after('deploy', 'reload:php-fpm');
  after('rollback', 'reload:php-fpm');

