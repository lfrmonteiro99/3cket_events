<?php declare(strict_types = 1);

// odsl-/var/www/tests
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1',
   'data' => 
  array (
    '/var/www/tests/Unit/ContainerTest.php' => 
    array (
      0 => '021a51d53bd0b3e5cccc93eeb3cf0d8c2d4ffdab',
      1 => 
      array (
        0 => 'tests\\unit\\containertest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\testpdoinstanceissingleton',
        1 => 'tests\\unit\\testeventrepositorycreation',
        2 => 'tests\\unit\\testeventcontrollercreation',
        3 => 'tests\\unit\\testcontainerinstancesarecached',
        4 => 'tests\\unit\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/Application/DTO/PaginatedResponseTest.php' => 
    array (
      0 => '3d372bcc3331a4fdf2accd7bc80ea1bf2f36fc43',
      1 => 
      array (
        0 => 'tests\\unit\\application\\dto\\paginatedresponsetest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\application\\dto\\testcreatepaginatedresponse',
        1 => 'tests\\unit\\application\\dto\\testcreatecalculatestotalpages',
        2 => 'tests\\unit\\application\\dto\\testcreatewithexactdivision',
        3 => 'tests\\unit\\application\\dto\\testhasnextpagetrue',
        4 => 'tests\\unit\\application\\dto\\testhasnextpagefalse',
        5 => 'tests\\unit\\application\\dto\\testhaspreviouspagetrue',
        6 => 'tests\\unit\\application\\dto\\testhaspreviouspagefalse',
        7 => 'tests\\unit\\application\\dto\\testgetnextpagereturnscorrectpage',
        8 => 'tests\\unit\\application\\dto\\testgetnextpagereturnsnullonlastpage',
        9 => 'tests\\unit\\application\\dto\\testgetpreviouspagereturnscorrectpage',
        10 => 'tests\\unit\\application\\dto\\testgetpreviouspagereturnsnullonfirstpage',
        11 => 'tests\\unit\\application\\dto\\testisemptytrue',
        12 => 'tests\\unit\\application\\dto\\testisemptyfalse',
        13 => 'tests\\unit\\application\\dto\\testgetstartitemwithdata',
        14 => 'tests\\unit\\application\\dto\\testgetstartitemwithemptydata',
        15 => 'tests\\unit\\application\\dto\\testgetenditemwithdata',
        16 => 'tests\\unit\\application\\dto\\testgetenditemwithpartialpage',
        17 => 'tests\\unit\\application\\dto\\testgetenditemwithemptydata',
        18 => 'tests\\unit\\application\\dto\\testtoarraystructure',
        19 => 'tests\\unit\\application\\dto\\testtoarraywithnonextpage',
        20 => 'tests\\unit\\application\\dto\\testtoarraywithnopreviouspage',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/Application/UseCase/GetEventByIdUseCaseTest.php' => 
    array (
      0 => '629ee9eb1c1d95db5cefd30861a42a3112b162ae',
      1 => 
      array (
        0 => 'tests\\unit\\application\\usecase\\geteventbyidusecasetest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\application\\usecase\\testexecutereturnseventasdtowhenfound',
        1 => 'tests\\unit\\application\\usecase\\testexecutereturnsnullwheneventnotfound',
        2 => 'tests\\unit\\application\\usecase\\testexecutecallsrepositorywithcorrecteventid',
        3 => 'tests\\unit\\application\\usecase\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/Application/UseCase/GetAllEventsUseCaseTest.php' => 
    array (
      0 => 'f2f6439a5d1aa98b5e244ff2b97904b188c7a4c7',
      1 => 
      array (
        0 => 'tests\\unit\\application\\usecase\\getalleventsusecasetest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\application\\usecase\\testexecutereturnsalleventsasdto',
        1 => 'tests\\unit\\application\\usecase\\testexecutereturnsemptyarraywhennoevents',
        2 => 'tests\\unit\\application\\usecase\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/Application/UseCase/CreateEventUseCaseTest.php' => 
    array (
      0 => 'bc5a438129d77df96130a856321c321c66f078d0',
      1 => 
      array (
        0 => 'tests\\unit\\application\\usecase\\createeventusecasetest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\application\\usecase\\testexecutecreateseventandreturnsdto',
        1 => 'tests\\unit\\application\\usecase\\testexecutecallsrepositorysaveonce',
        2 => 'tests\\unit\\application\\usecase\\testexecutecreateseventwithcorrectvalueobjects',
        3 => 'tests\\unit\\application\\usecase\\testexecutereturnscorrectdtostructure',
        4 => 'tests\\unit\\application\\usecase\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/Application/UseCase/GetPaginatedEventsUseCaseTest.php' => 
    array (
      0 => '21beac82b13fddb39c21d98b1b49e232e18a3405',
      1 => 
      array (
        0 => 'tests\\unit\\application\\usecase\\getpaginatedeventsusecasetest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\application\\usecase\\testexecutereturnspaginatedevents',
        1 => 'tests\\unit\\application\\usecase\\testexecutewithemptyresults',
        2 => 'tests\\unit\\application\\usecase\\testexecutewithdifferentpaginationparams',
        3 => 'tests\\unit\\application\\usecase\\testexecutereturnscorrecteventdtos',
        4 => 'tests\\unit\\application\\usecase\\setup',
        5 => 'tests\\unit\\application\\usecase\\createevent',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/Application/Query/PaginationQueryTest.php' => 
    array (
      0 => '98ada433bc050aaf91dc5621556a92fe52e980dc',
      1 => 
      array (
        0 => 'tests\\unit\\application\\query\\paginationquerytest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\application\\query\\testvalidpaginationqueryiscreated',
        1 => 'tests\\unit\\application\\query\\testdefaultvaluesareused',
        2 => 'tests\\unit\\application\\query\\testinvalidpagethrowsexception',
        3 => 'tests\\unit\\application\\query\\testinvalidpagesizethrowsexception',
        4 => 'tests\\unit\\application\\query\\testpagesizetoolargethrowsexception',
        5 => 'tests\\unit\\application\\query\\testinvalidsortdirectionthrowsexception',
        6 => 'tests\\unit\\application\\query\\testinvalidsortbythrowsexception',
        7 => 'tests\\unit\\application\\query\\testgetoffsetcalculation',
        8 => 'tests\\unit\\application\\query\\testgetlimitreturnspagesize',
        9 => 'tests\\unit\\application\\query\\testgetsortdirectionnormalizescase',
        10 => 'tests\\unit\\application\\query\\testgetcachekeyisgenerated',
        11 => 'tests\\unit\\application\\query\\testvalidsortfields',
        12 => 'tests\\unit\\application\\query\\testsortdirectioncaseinsensitive',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/InMemoryCacheTest.php' => 
    array (
      0 => 'fa26ee7b18296690e2d866569cc46fcdd149eb8f',
      1 => 
      array (
        0 => 'tests\\unit\\inmemorycachetest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\testsetandget',
        1 => 'tests\\unit\\testgetnonexistentkey',
        2 => 'tests\\unit\\testexists',
        3 => 'tests\\unit\\testdelete',
        4 => 'tests\\unit\\testclear',
        5 => 'tests\\unit\\testttlexpiration',
        6 => 'tests\\unit\\testzerottlneverexpires',
        7 => 'tests\\unit\\testmultipleoperations',
        8 => 'tests\\unit\\teststats',
        9 => 'tests\\unit\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/RouterTest.php' => 
    array (
      0 => '2edf111320bc0ff42220fca273da66c29aa00bac',
      1 => 
      array (
        0 => 'tests\\unit\\routertest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\testaddroute',
        1 => 'tests\\unit\\testgetmethod',
        2 => 'tests\\unit\\testroutenotfound',
        3 => 'tests\\unit\\testparameterizedroute',
        4 => 'tests\\unit\\testextractparameters',
        5 => 'tests\\unit\\testextractmultipleparameters',
        6 => 'tests\\unit\\testparameterizedroutewithnoparameters',
        7 => 'tests\\unit\\testparameterizedroutematching',
        8 => 'tests\\unit\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/Presentation/Controller/EventControllerTest.php' => 
    array (
      0 => '83c8a2e12621341c8b868224be9d74b44ee39452',
      1 => 
      array (
        0 => 'tests\\unit\\presentation\\controller\\eventcontrollertest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\presentation\\controller\\testindexreturnsallevents',
        1 => 'tests\\unit\\presentation\\controller\\testindexreturnsemptyarraywhennoevents',
        2 => 'tests\\unit\\presentation\\controller\\testshowreturnsspecificevent',
        3 => 'tests\\unit\\presentation\\controller\\testshowreturnserrorwheneventnotfound',
        4 => 'tests\\unit\\presentation\\controller\\testshowwithvalididparameter',
        5 => 'tests\\unit\\presentation\\controller\\testshowwithinvalididparameter',
        6 => 'tests\\unit\\presentation\\controller\\testshowwithnegativeidparameter',
        7 => 'tests\\unit\\presentation\\controller\\testdebugreturnssysteminformation',
        8 => 'tests\\unit\\presentation\\controller\\testpaginatedreturnsfirstpage',
        9 => 'tests\\unit\\presentation\\controller\\testpaginatedwithqueryparameters',
        10 => 'tests\\unit\\presentation\\controller\\testpaginatedwithinvalidparameters',
        11 => 'tests\\unit\\presentation\\controller\\setup',
        12 => 'tests\\unit\\presentation\\controller\\teardown',
        13 => 'tests\\unit\\presentation\\controller\\createeventdto',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/Infrastructure/Repository/DatabaseEventRepositoryTest.php' => 
    array (
      0 => 'f4d05b32cfe3dd0af1f5c95e9564bf45d4bdada5',
      1 => 
      array (
        0 => 'tests\\unit\\infrastructure\\repository\\databaseeventrepositorytest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\infrastructure\\repository\\testfindallreturnsallevents',
        1 => 'tests\\unit\\infrastructure\\repository\\testfindbyidreturnseventwhenfound',
        2 => 'tests\\unit\\infrastructure\\repository\\testfindbyidreturnsnullwhennotfound',
        3 => 'tests\\unit\\infrastructure\\repository\\testcountreturnscorrectnumber',
        4 => 'tests\\unit\\infrastructure\\repository\\testsaveinsertsneweventwhennoid',
        5 => 'tests\\unit\\infrastructure\\repository\\testsaveupdatesexistingeventwhenhasid',
        6 => 'tests\\unit\\infrastructure\\repository\\testdeleteremovesevent',
        7 => 'tests\\unit\\infrastructure\\repository\\testnextidreturnsneweventid',
        8 => 'tests\\unit\\infrastructure\\repository\\testprepareiscalledonceperquery',
        9 => 'tests\\unit\\infrastructure\\repository\\teststatementexecuteiscalledwithcorrectparameters',
        10 => 'tests\\unit\\infrastructure\\repository\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/Infrastructure/Repository/CachedEventRepositoryTest.php' => 
    array (
      0 => '97a4c2d19d0fe0280ce550ba12974e6b592f5bde',
      1 => 
      array (
        0 => 'tests\\unit\\infrastructure\\repository\\cachedeventrepositorytest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\infrastructure\\repository\\testfindallreturnscachedresultwhenavailable',
        1 => 'tests\\unit\\infrastructure\\repository\\testfindallfetchesfromrepositorywhennotcached',
        2 => 'tests\\unit\\infrastructure\\repository\\testfindbyidreturnscachedresultwhenavailable',
        3 => 'tests\\unit\\infrastructure\\repository\\testfindbyidfetchesfromrepositorywhennotcached',
        4 => 'tests\\unit\\infrastructure\\repository\\testfindbyidreturnsnullwheneventnotfound',
        5 => 'tests\\unit\\infrastructure\\repository\\testcountreturnscachedresultwhenavailable',
        6 => 'tests\\unit\\infrastructure\\repository\\testcountfetchesfromrepositorywhennotcached',
        7 => 'tests\\unit\\infrastructure\\repository\\testsaveinvalidatescacheandcallsinnerrepository',
        8 => 'tests\\unit\\infrastructure\\repository\\testdeleteinvalidatescacheandcallsinnerrepository',
        9 => 'tests\\unit\\infrastructure\\repository\\testnextidcallsinnerrepositorydirectly',
        10 => 'tests\\unit\\infrastructure\\repository\\testclearcacheinvalidatesalleventcaches',
        11 => 'tests\\unit\\infrastructure\\repository\\testgetcachestatsreturnsdefaultwhennotavailable',
        12 => 'tests\\unit\\infrastructure\\repository\\testcachekeygenerationforevents',
        13 => 'tests\\unit\\infrastructure\\repository\\testmultiplecacheoperationsinsequence',
        14 => 'tests\\unit\\infrastructure\\repository\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/Domain/Service/EventDomainServiceTest.php' => 
    array (
      0 => '9f20cbffd6fb099ddc6928f73f02106cb9603401',
      1 => 
      array (
        0 => 'tests\\unit\\domain\\service\\eventdomainservicetest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\domain\\service\\testfindeventswithinradiusreturnseventswithinradius',
        1 => 'tests\\unit\\domain\\service\\testfindeventswithinradiusreturnsemptywhennoeventswithinradius',
        2 => 'tests\\unit\\domain\\service\\testfindnearesteventreturnsclosestevent',
        3 => 'tests\\unit\\domain\\service\\testfindnearesteventreturnsnullwhennoevents',
        4 => 'tests\\unit\\domain\\service\\testiseventnameuniquereturnstruewhennameisunique',
        5 => 'tests\\unit\\domain\\service\\testiseventnameuniquereturnsfalsewhennameexists',
        6 => 'tests\\unit\\domain\\service\\testcalculateeventscenterreturnscorrectcoordinates',
        7 => 'tests\\unit\\domain\\service\\testcalculateeventscenterreturnsnullwhennoevents',
        8 => 'tests\\unit\\domain\\service\\testrepositoryiscalledonceforfindeventswithinradius',
        9 => 'tests\\unit\\domain\\service\\testrepositoryiscalledonceforfindnearestevent',
        10 => 'tests\\unit\\domain\\service\\testrepositoryiscalledonceforiseventnameunique',
        11 => 'tests\\unit\\domain\\service\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/Domain/ValueObject/CoordinatesTest.php' => 
    array (
      0 => '6cff6772d8235fbaeaebe3a76a7e98a55e393f0b',
      1 => 
      array (
        0 => 'tests\\unit\\domain\\valueobject\\coordinatestest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\domain\\valueobject\\testvalidcoordinatescanbecreated',
        1 => 'tests\\unit\\domain\\valueobject\\testinvalidlatitudetoohighthrowsexception',
        2 => 'tests\\unit\\domain\\valueobject\\testinvalidlatitudetoolowthrowsexception',
        3 => 'tests\\unit\\domain\\valueobject\\testinvalidlongitudetoohighthrowsexception',
        4 => 'tests\\unit\\domain\\valueobject\\testinvalidlongitudetoolowthrowsexception',
        5 => 'tests\\unit\\domain\\valueobject\\testboundarylatitudevaluesarevalid',
        6 => 'tests\\unit\\domain\\valueobject\\testboundarylongitudevaluesarevalid',
        7 => 'tests\\unit\\domain\\valueobject\\testcoordinatesequality',
        8 => 'tests\\unit\\domain\\valueobject\\testdistancecalculation',
        9 => 'tests\\unit\\domain\\valueobject\\testdistancetosamelocationiszero',
        10 => 'tests\\unit\\domain\\valueobject\\testtostring',
        11 => 'tests\\unit\\domain\\valueobject\\testtoarray',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/tests/Unit/Domain/Entity/EventTest.php' => 
    array (
      0 => '1c603895382db3fbe0c8459cf950dff2f6ae1cf5',
      1 => 
      array (
        0 => 'tests\\unit\\domain\\entity\\eventtest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\domain\\entity\\testeventcanbecreated',
        1 => 'tests\\unit\\domain\\entity\\testeventwithoutidraisesdomainevent',
        2 => 'tests\\unit\\domain\\entity\\testeventupdatename',
        3 => 'tests\\unit\\domain\\entity\\testeventupdatenamewithsamenamedoesnotraisedomainevent',
        4 => 'tests\\unit\\domain\\entity\\testeventupdatelocation',
        5 => 'tests\\unit\\domain\\entity\\testeventupdatecoordinates',
        6 => 'tests\\unit\\domain\\entity\\testeventdistanceto',
        7 => 'tests\\unit\\domain\\entity\\testeventequals',
        8 => 'tests\\unit\\domain\\entity\\testeventtoarray',
        9 => 'tests\\unit\\domain\\entity\\testcleardomainevents',
        10 => 'tests\\unit\\domain\\entity\\createtestevent',
      ),
      3 => 
      array (
      ),
    ),
  ),
));