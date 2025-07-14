<?php declare(strict_types = 1);

// odsl-/Users/luis.monteiro.ext/Documents/3cket_events/tests
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1',
   'data' => 
  array (
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/ContainerTest.php' => 
    array (
      0 => 'c6397d589097af31397603f2c67524b8ce02b30a',
      1 => 
      array (
        0 => 'tests\\unit\\containertest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\testcontainercanbecreated',
        1 => 'tests\\unit\\testcontainercanbindandretrieveservices',
        2 => 'tests\\unit\\testcontainerreturnssameinstanceforsingleton',
        3 => 'tests\\unit\\testcontainerthrowsexceptionforunboundservice',
        4 => 'tests\\unit\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/InMemoryCacheTest.php' => 
    array (
      0 => 'e88e151e4acc693f0b29763640353dda6d604d02',
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
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/RouterTest.php' => 
    array (
      0 => '65e7e598b252a08cac588633f18d1515bdea44d1',
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
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Application/DTO/PaginatedResponseTest.php' => 
    array (
      0 => '1a56cb1bccc8d587c54252e9691845e02b7c110f',
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
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Application/Service/EventServiceTest.php' => 
    array (
      0 => 'f3b9c258b9696b01c6638ee437ea3cba7ec267a2',
      1 => 
      array (
        0 => 'tests\\unit\\application\\service\\eventservicetest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\application\\service\\testgetalleventsreturnspaginatedresponse',
        1 => 'tests\\unit\\application\\service\\testgeteventbyidreturnseventdto',
        2 => 'tests\\unit\\application\\service\\testgeteventbyidreturnsnullwhennotfound',
        3 => 'tests\\unit\\application\\service\\testgeteventcountreturnscorrectcount',
        4 => 'tests\\unit\\application\\service\\testeventexistsreturnstruewheneventexists',
        5 => 'tests\\unit\\application\\service\\testeventexistsreturnsfalsewheneventdoesnotexist',
        6 => 'tests\\unit\\application\\service\\testeventexistsreturnsfalseforinvalidid',
        7 => 'tests\\unit\\application\\service\\testvalidatepaginationbusinessrulesthrowsexceptionforlargepagesize',
        8 => 'tests\\unit\\application\\service\\testvalidateeventidthrowsexceptionforinvalidid',
        9 => 'tests\\unit\\application\\service\\testvalidateeventidthrowsexceptionfortoolargeid',
        10 => 'tests\\unit\\application\\service\\setup',
        11 => 'tests\\unit\\application\\service\\createevent',
      ),
      3 => 
      array (
      ),
    ),
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Application/Query/PaginationQueryTest.php' => 
    array (
      0 => '752c8e061e8483bfdd2f6c2adacbd5c33a8a802a',
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
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Application/UseCase/GetAllEventsUseCaseTest.php' => 
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
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Application/UseCase/GetEventByIdUseCaseTest.php' => 
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
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Application/UseCase/GetPaginatedEventsUseCaseTest.php' => 
    array (
      0 => '331bf18b4ecda33279833177a625c36d0b54a15e',
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
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Infrastructure/Repository/DatabaseEventRepositoryTest.php' => 
    array (
      0 => '0ff3fddb5f9c891a54b6440789d04e6cf0198672',
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
        4 => 'tests\\unit\\infrastructure\\repository\\testprepareiscalledonceperquery',
        5 => 'tests\\unit\\infrastructure\\repository\\teststatementexecuteiscalledwithcorrectparameters',
        6 => 'tests\\unit\\infrastructure\\repository\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Infrastructure/Repository/CachedEventRepositoryTest.php' => 
    array (
      0 => '24c4326f8676d29ee208d4920836e6b2a8e25cb3',
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
        7 => 'tests\\unit\\infrastructure\\repository\\testcachekeygenerationforevents',
        8 => 'tests\\unit\\infrastructure\\repository\\testmultiplecacheoperationsinsequence',
        9 => 'tests\\unit\\infrastructure\\repository\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Domain/ValueObject/CoordinatesTest.php' => 
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
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Domain/Entity/EventTest.php' => 
    array (
      0 => '3d5ca7f67623b3c6898bee5ad4c79ce4dd87dc45',
      1 => 
      array (
        0 => 'tests\\unit\\domain\\entity\\eventtest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\domain\\entity\\testeventcanbecreated',
        1 => 'tests\\unit\\domain\\entity\\testeventdistanceto',
        2 => 'tests\\unit\\domain\\entity\\testeventequals',
        3 => 'tests\\unit\\domain\\entity\\testeventtoarray',
        4 => 'tests\\unit\\domain\\entity\\createtestevent',
      ),
      3 => 
      array (
      ),
    ),
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Domain/Service/EventDomainServiceTest.php' => 
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
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Presentation/Controller/EventControllerTest.php' => 
    array (
      0 => '77487d6fdfa2e33dec7579188f2660cd30f60eb0',
      1 => 
      array (
        0 => 'tests\\unit\\presentation\\controller\\eventcontrollertest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\presentation\\controller\\testsearchmethodusesvalidatorbagandsearchservice',
        1 => 'tests\\unit\\presentation\\controller\\testsearchmethodhandlesinvalidargumentexception',
        2 => 'tests\\unit\\presentation\\controller\\testsearchmethodhandlesgenericexception',
        3 => 'tests\\unit\\presentation\\controller\\testshowmethodusesvalidatorbag',
        4 => 'tests\\unit\\presentation\\controller\\testshowmethodhandlesmissingid',
        5 => 'tests\\unit\\presentation\\controller\\testshowmethodhandlesinvalidid',
        6 => 'tests\\unit\\presentation\\controller\\testindexmethodusesvalidatorbag',
        7 => 'tests\\unit\\presentation\\controller\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Presentation/Controller/EventControllerServiceTest.php' => 
    array (
      0 => 'd6ab3ba8bccdf8ef0b49d3331a7be97f8bf31b46',
      1 => 
      array (
        0 => 'tests\\unit\\presentation\\controller\\eventcontrollerservicetest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\presentation\\controller\\testindexusesservicelayer',
        1 => 'tests\\unit\\presentation\\controller\\testshowusesservicelayer',
        2 => 'tests\\unit\\presentation\\controller\\testshowreturnsnotfoundwhenservicereturnsnull',
        3 => 'tests\\unit\\presentation\\controller\\testdebugusesservicelayer',
        4 => 'tests\\unit\\presentation\\controller\\testshowrequiresidparameter',
        5 => 'tests\\unit\\presentation\\controller\\testindexwithinvalidparameters',
        6 => 'tests\\unit\\presentation\\controller\\setup',
        7 => 'tests\\unit\\presentation\\controller\\createeventdto',
      ),
      3 => 
      array (
      ),
    ),
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Application/Service/SearchServiceTest.php' => 
    array (
      0 => '143abae88898fe31b14f0b3decdeba8f8d0fccb6',
      1 => 
      array (
        0 => 'tests\\unit\\application\\service\\searchservicetest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\application\\service\\testsearcheventsformattedreturnscorrectstructure',
        1 => 'tests\\unit\\application\\service\\testsearcheventsformattedwithgeographicsearch',
        2 => 'tests\\unit\\application\\service\\testsearcheventsformattedwithdatefilter',
        3 => 'tests\\unit\\application\\service\\testsearcheventsformattedwithnofilters',
        4 => 'tests\\unit\\application\\service\\testsearcheventsformattedcallsoriginalsearcheventsmethod',
        5 => 'tests\\unit\\application\\service\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Unit/Infrastructure/Validation/ValidatorBagTest.php' => 
    array (
      0 => '90bae821b1158914aa6d1df1ef14f76bb1f85843',
      1 => 
      array (
        0 => 'tests\\unit\\infrastructure\\validation\\validatorbagtest',
      ),
      2 => 
      array (
        0 => 'tests\\unit\\infrastructure\\validation\\testeventidreturnscorrectvalidator',
        1 => 'tests\\unit\\infrastructure\\validation\\testpaginationreturnscorrectvalidator',
        2 => 'tests\\unit\\infrastructure\\validation\\testeventidvalidatorworksthroughbag',
        3 => 'tests\\unit\\infrastructure\\validation\\testpaginationvalidatorworksthroughbag',
        4 => 'tests\\unit\\infrastructure\\validation\\setup',
      ),
      3 => 
      array (
      ),
    ),
    '/Users/luis.monteiro.ext/Documents/3cket_events/tests/Integration/EndpointTest.php' => 
    array (
      0 => '9497f6f0ea4dea9f2c28c0451b84c2a3d4839d01',
      1 => 
      array (
        0 => 'tests\\integration\\endpointtest',
      ),
      2 => 
      array (
        0 => 'tests\\integration\\testeventsendpointwithvalidparameters',
        1 => 'tests\\integration\\testeventsendpointwithdefaultparameters',
        2 => 'tests\\integration\\testeventsendpointwithinvalidpage',
        3 => 'tests\\integration\\testeventsendpointwithinvalidpagesize',
        4 => 'tests\\integration\\testeventsendpointwithinvalidsortdirection',
        5 => 'tests\\integration\\testshoweventwithvalidid',
        6 => 'tests\\integration\\testshoweventwithinvalidid',
        7 => 'tests\\integration\\testshoweventwithnonexistentid',
        8 => 'tests\\integration\\testshoweventwithoutid',
        9 => 'tests\\integration\\testsearchendpointwithtextsearch',
        10 => 'tests\\integration\\testsearchendpointwithlocationfilter',
        11 => 'tests\\integration\\testsearchendpointwithgeographicsearch',
        12 => 'tests\\integration\\testsearchendpointwithdatefilter',
        13 => 'tests\\integration\\testsearchendpointwithallfilters',
        14 => 'tests\\integration\\testsearchendpointwithinvalidpage',
        15 => 'tests\\integration\\testsearchendpointwithinvalidcoordinates',
        16 => 'tests\\integration\\testsearchendpointwithinvaliddaterange',
        17 => 'tests\\integration\\testnearbyendpointwithvalidcoordinates',
        18 => 'tests\\integration\\testnearbyendpointwithdefaultparameters',
        19 => 'tests\\integration\\testnearbyendpointwithoutlatitude',
        20 => 'tests\\integration\\testnearbyendpointwithoutlongitude',
        21 => 'tests\\integration\\testnearbyendpointwithinvalidcoordinates',
        22 => 'tests\\integration\\testnearbyendpointwithinvalidradius',
        23 => 'tests\\integration\\testsuggestionsendpointwithquery',
        24 => 'tests\\integration\\testsuggestionsendpointwithoutquery',
        25 => 'tests\\integration\\testsuggestionsendpointwithdefaultlimit',
        26 => 'tests\\integration\\testsuggestionsendpointwithcustomlimit',
        27 => 'tests\\integration\\testdebugendpoint',
        28 => 'tests\\integration\\testcacheendpointwithstatsaction',
        29 => 'tests\\integration\\testcacheendpointwithclearaction',
        30 => 'tests\\integration\\testcacheendpointwithdefaultaction',
        31 => 'tests\\integration\\testcacheendpointwithinvalidaction',
        32 => 'tests\\integration\\testcacheanalyticsendpoint',
        33 => 'tests\\integration\\testcachewarmupendpoint',
        34 => 'tests\\integration\\testinvalidatecacheendpoint',
        35 => 'tests\\integration\\testinvalidateeventcacheendpointwithvalidid',
        36 => 'tests\\integration\\testinvalidateeventcacheendpointwithoutid',
        37 => 'tests\\integration\\testinvalidatesearchcacheendpoint',
        38 => 'tests\\integration\\testnonexistentendpoint',
        39 => 'tests\\integration\\testnonexistentmethod',
        40 => 'tests\\integration\\testeventsendpointwithmaximumpagesize',
        41 => 'tests\\integration\\testeventsendpointwithlargepagenumber',
        42 => 'tests\\integration\\testsearchendpointwithemptysearchterm',
        43 => 'tests\\integration\\testsearchendpointwithspecialcharacters',
        44 => 'tests\\integration\\testnearbyendpointwithboundarycoordinates',
        45 => 'tests\\integration\\setup',
        46 => 'tests\\integration\\teardown',
        47 => 'tests\\integration\\makerequest',
      ),
      3 => 
      array (
      ),
    ),
  ),
));