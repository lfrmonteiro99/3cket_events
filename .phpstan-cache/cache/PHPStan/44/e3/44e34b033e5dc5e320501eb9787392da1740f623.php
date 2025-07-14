<?php declare(strict_types = 1);

// odsl-/var/www/src
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1',
   'data' => 
  array (
    '/var/www/src/Service/Container.php' => 
    array (
      0 => 'c3fe17eca4164229211b21bbc641c53bd1467a35',
      1 => 
      array (
        0 => 'app\\service\\container',
      ),
      2 => 
      array (
        0 => 'app\\service\\__construct',
        1 => 'app\\service\\bind',
        2 => 'app\\service\\get',
        3 => 'app\\service\\configure',
        4 => 'app\\service\\registerproviders',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Exception/RouteNotFoundException.php' => 
    array (
      0 => 'b14c4e6f24d53025923f0d02415fca67efdef486',
      1 => 
      array (
        0 => 'app\\exception\\routenotfoundexception',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Exception/FileNotFoundException.php' => 
    array (
      0 => 'f62286046999ba7d8214f38bb0b7254de284aadd',
      1 => 
      array (
        0 => 'app\\exception\\filenotfoundexception',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Exception/EventNotFoundException.php' => 
    array (
      0 => '877c5d737de0db190c82a04f2cb799b3c3934a0d',
      1 => 
      array (
        0 => 'app\\exception\\eventnotfoundexception',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Router/Route.php' => 
    array (
      0 => 'f6ec0b16b0d9685c1205427a3155b06186ab4030',
      1 => 
      array (
        0 => 'app\\router\\route',
      ),
      2 => 
      array (
        0 => 'app\\router\\__construct',
        1 => 'app\\router\\getmethod',
        2 => 'app\\router\\getpath',
        3 => 'app\\router\\getcontroller',
        4 => 'app\\router\\getaction',
        5 => 'app\\router\\matches',
        6 => 'app\\router\\matchespath',
        7 => 'app\\router\\extractparameters',
        8 => 'app\\router\\converttoregex',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Router/Router.php' => 
    array (
      0 => '12169b0c89cb661f81605026064643dd7cef9880',
      1 => 
      array (
        0 => 'app\\router\\router',
      ),
      2 => 
      array (
        0 => 'app\\router\\__construct',
        1 => 'app\\router\\addroute',
        2 => 'app\\router\\get',
        3 => 'app\\router\\resolve',
        4 => 'app\\router\\dispatch',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Router/HttpMethod.php' => 
    array (
      0 => '775cd41af632fa4ec5ed8cb7c9eb2d2e70d6619e',
      1 => 
      array (
        0 => 'app\\router\\httpmethod',
      ),
      2 => 
      array (
        0 => 'app\\router\\fromstring',
        1 => 'app\\router\\issafe',
        2 => 'app\\router\\isidempotent',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/DTO/EventDto.php' => 
    array (
      0 => '94a6a6930f521cdad933976dd6f6227810064e93',
      1 => 
      array (
        0 => 'app\\application\\dto\\eventdto',
      ),
      2 => 
      array (
        0 => 'app\\application\\dto\\__construct',
        1 => 'app\\application\\dto\\toarray',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/DTO/PaginatedResponse.php' => 
    array (
      0 => 'ff4a01f0f1f488a0c8434a57af41cf3b51d5c255',
      1 => 
      array (
        0 => 'app\\application\\dto\\paginatedresponse',
      ),
      2 => 
      array (
        0 => 'app\\application\\dto\\__construct',
        1 => 'app\\application\\dto\\create',
        2 => 'app\\application\\dto\\hasnextpage',
        3 => 'app\\application\\dto\\haspreviouspage',
        4 => 'app\\application\\dto\\getnextpage',
        5 => 'app\\application\\dto\\getpreviouspage',
        6 => 'app\\application\\dto\\isempty',
        7 => 'app\\application\\dto\\getstartitem',
        8 => 'app\\application\\dto\\getenditem',
        9 => 'app\\application\\dto\\toarray',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/Mapper/EventMapper.php' => 
    array (
      0 => '6bfca643279363692518fe7c1423ea85d1eec08c',
      1 => 
      array (
        0 => 'app\\application\\mapper\\eventmapper',
      ),
      2 => 
      array (
        0 => 'app\\application\\mapper\\todto',
        1 => 'app\\application\\mapper\\todtoarray',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/UseCase/GetEventByIdUseCase.php' => 
    array (
      0 => '7003bc52f10f986329118d5a864d016a9c2f21e6',
      1 => 
      array (
        0 => 'app\\application\\usecase\\geteventbyidusecase',
      ),
      2 => 
      array (
        0 => 'app\\application\\usecase\\__construct',
        1 => 'app\\application\\usecase\\execute',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/UseCase/GetEventByIdUseCaseInterface.php' => 
    array (
      0 => '0125e2c2b85e42a78f1c419c6f9cc1988eb1244f',
      1 => 
      array (
        0 => 'app\\application\\usecase\\geteventbyidusecaseinterface',
      ),
      2 => 
      array (
        0 => 'app\\application\\usecase\\execute',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/UseCase/GetAllEventsUseCase.php' => 
    array (
      0 => 'fffeee567b5e9d56d57232f5a5e43e89b1dab27e',
      1 => 
      array (
        0 => 'app\\application\\usecase\\getalleventsusecase',
      ),
      2 => 
      array (
        0 => 'app\\application\\usecase\\__construct',
        1 => 'app\\application\\usecase\\execute',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/UseCase/GetPaginatedEventsUseCaseInterface.php' => 
    array (
      0 => '06fe1e3cd7903a5fd1b9416c63a25c0a41c4c999',
      1 => 
      array (
        0 => 'app\\application\\usecase\\getpaginatedeventsusecaseinterface',
      ),
      2 => 
      array (
        0 => 'app\\application\\usecase\\execute',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/UseCase/GetAllEventsUseCaseInterface.php' => 
    array (
      0 => 'ea601232dbb0fecefa48d86f40749be275462040',
      1 => 
      array (
        0 => 'app\\application\\usecase\\getalleventsusecaseinterface',
      ),
      2 => 
      array (
        0 => 'app\\application\\usecase\\execute',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/UseCase/GetPaginatedEventsUseCase.php' => 
    array (
      0 => 'c50602f27794cb297a93fac457169b16e48c578e',
      1 => 
      array (
        0 => 'app\\application\\usecase\\getpaginatedeventsusecase',
      ),
      2 => 
      array (
        0 => 'app\\application\\usecase\\__construct',
        1 => 'app\\application\\usecase\\execute',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/Query/GetPaginatedEventsQuery.php' => 
    array (
      0 => '340c7bda2a6a08184cc8d597f959b300939bb77c',
      1 => 
      array (
        0 => 'app\\application\\query\\getpaginatedeventsquery',
      ),
      2 => 
      array (
        0 => 'app\\application\\query\\__construct',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/Query/PaginationQuery.php' => 
    array (
      0 => 'c6bf4b592c60990ad4779321bfc6406b2bb6cf0f',
      1 => 
      array (
        0 => 'app\\application\\query\\paginationquery',
      ),
      2 => 
      array (
        0 => 'app\\application\\query\\__construct',
        1 => 'app\\application\\query\\getoffset',
        2 => 'app\\application\\query\\getlimit',
        3 => 'app\\application\\query\\getsortdirection',
        4 => 'app\\application\\query\\getcachekey',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/Query/GetEventByIdQuery.php' => 
    array (
      0 => 'd02ee3727ebfbaf44abadcc982873883e7271185',
      1 => 
      array (
        0 => 'app\\application\\query\\geteventbyidquery',
      ),
      2 => 
      array (
        0 => 'app\\application\\query\\__construct',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/Query/GetAllEventsQuery.php' => 
    array (
      0 => 'db20a737ca5141364645e6034ab5a3c0a5c82b7d',
      1 => 
      array (
        0 => 'app\\application\\query\\getalleventsquery',
      ),
      2 => 
      array (
        0 => 'app\\application\\query\\__construct',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Presentation/Response/JsonResponse.php' => 
    array (
      0 => '3a26e29267332f9ce1a37a1f8f4cc5941a9538b3',
      1 => 
      array (
        0 => 'app\\presentation\\response\\jsonresponse',
      ),
      2 => 
      array (
        0 => 'app\\presentation\\response\\__construct',
        1 => 'app\\presentation\\response\\send',
        2 => 'app\\presentation\\response\\success',
        3 => 'app\\presentation\\response\\error',
        4 => 'app\\presentation\\response\\notfound',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Presentation/Response/HttpStatus.php' => 
    array (
      0 => 'dde6f51791f83f3a6158523fe1ae8857130c2992',
      1 => 
      array (
        0 => 'app\\presentation\\response\\httpstatus',
      ),
      2 => 
      array (
        0 => 'app\\presentation\\response\\issuccess',
        1 => 'app\\presentation\\response\\isclienterror',
        2 => 'app\\presentation\\response\\isservererror',
        3 => 'app\\presentation\\response\\getreasonphrase',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Presentation/Controller/EventController.php' => 
    array (
      0 => '53ef629f6d3d3d61c45145b8b4dc852a98a14ba4',
      1 => 
      array (
        0 => 'app\\presentation\\controller\\eventcontroller',
      ),
      2 => 
      array (
        0 => 'app\\presentation\\controller\\__construct',
        1 => 'app\\presentation\\controller\\index',
        2 => 'app\\presentation\\controller\\show',
        3 => 'app\\presentation\\controller\\debug',
        4 => 'app\\presentation\\controller\\cache',
        5 => 'app\\presentation\\controller\\search',
        6 => 'app\\presentation\\controller\\nearby',
        7 => 'app\\presentation\\controller\\suggestions',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Database/DatabaseConnection.php' => 
    array (
      0 => '4b7e66aac9956a777025d6e74b54f34e81e8c073',
      1 => 
      array (
        0 => 'app\\infrastructure\\database\\databaseconnection',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\database\\__construct',
        1 => 'app\\infrastructure\\database\\createconnection',
        2 => 'app\\infrastructure\\database\\fromenvironment',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Repository/CsvEventRepository.php' => 
    array (
      0 => '1a4073ffbae4f12972a6e77ac2441de35b655a49',
      1 => 
      array (
        0 => 'app\\infrastructure\\repository\\csveventrepository',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\repository\\__construct',
        1 => 'app\\infrastructure\\repository\\findall',
        2 => 'app\\infrastructure\\repository\\findpaginated',
        3 => 'app\\infrastructure\\repository\\findbyid',
        4 => 'app\\infrastructure\\repository\\search',
        5 => 'app\\infrastructure\\repository\\countsearch',
        6 => 'app\\infrastructure\\repository\\count',
        7 => 'app\\infrastructure\\repository\\loadeventsifneeded',
        8 => 'app\\infrastructure\\repository\\loadevents',
        9 => 'app\\infrastructure\\repository\\applysearchfilters',
        10 => 'app\\infrastructure\\repository\\sortevents',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Repository/DatabaseEventRepository.php' => 
    array (
      0 => '1970d44e16c03e7cd87b739304faba0c636039f9',
      1 => 
      array (
        0 => 'app\\infrastructure\\repository\\databaseeventrepository',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\repository\\__construct',
        1 => 'app\\infrastructure\\repository\\findall',
        2 => 'app\\infrastructure\\repository\\findpaginated',
        3 => 'app\\infrastructure\\repository\\findbyid',
        4 => 'app\\infrastructure\\repository\\search',
        5 => 'app\\infrastructure\\repository\\countsearch',
        6 => 'app\\infrastructure\\repository\\count',
        7 => 'app\\infrastructure\\repository\\maprowtoevent',
        8 => 'app\\infrastructure\\repository\\mapsortcolumn',
        9 => 'app\\infrastructure\\repository\\buildsearchquery',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Repository/CachedEventRepository.php' => 
    array (
      0 => '6032b73c2c83fc06a431f540d2b1c40ebbf371b4',
      1 => 
      array (
        0 => 'app\\infrastructure\\repository\\cachedeventrepository',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\repository\\__construct',
        1 => 'app\\infrastructure\\repository\\findall',
        2 => 'app\\infrastructure\\repository\\findpaginated',
        3 => 'app\\infrastructure\\repository\\findbyid',
        4 => 'app\\infrastructure\\repository\\search',
        5 => 'app\\infrastructure\\repository\\countsearch',
        6 => 'app\\infrastructure\\repository\\count',
        7 => 'app\\infrastructure\\repository\\clearcache',
        8 => 'app\\infrastructure\\repository\\getcachestats',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Cache/RedisCache.php' => 
    array (
      0 => '7a3e503b3205f09dd41d1ccf4820493301e56891',
      1 => 
      array (
        0 => 'app\\infrastructure\\cache\\rediscache',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\cache\\__construct',
        1 => 'app\\infrastructure\\cache\\get',
        2 => 'app\\infrastructure\\cache\\set',
        3 => 'app\\infrastructure\\cache\\delete',
        4 => 'app\\infrastructure\\cache\\clear',
        5 => 'app\\infrastructure\\cache\\exists',
        6 => 'app\\infrastructure\\cache\\getmultiple',
        7 => 'app\\infrastructure\\cache\\setmultiple',
        8 => 'app\\infrastructure\\cache\\getstats',
        9 => 'app\\infrastructure\\cache\\createfromenvironment',
        10 => 'app\\infrastructure\\cache\\prefixkey',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Cache/CacheStrategy.php' => 
    array (
      0 => '2ca848bdffa22dcb7edbc1c2d836bcbec51db5c6',
      1 => 
      array (
        0 => 'app\\infrastructure\\cache\\cachestrategy',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\cache\\isnone',
        1 => 'app\\infrastructure\\cache\\ismemory',
        2 => 'app\\infrastructure\\cache\\fromstring',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Cache/NullCache.php' => 
    array (
      0 => '149ea83501bc61a042c635e0fb724b5dad92812d',
      1 => 
      array (
        0 => 'app\\infrastructure\\cache\\nullcache',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\cache\\get',
        1 => 'app\\infrastructure\\cache\\set',
        2 => 'app\\infrastructure\\cache\\delete',
        3 => 'app\\infrastructure\\cache\\clear',
        4 => 'app\\infrastructure\\cache\\exists',
        5 => 'app\\infrastructure\\cache\\getmultiple',
        6 => 'app\\infrastructure\\cache\\setmultiple',
        7 => 'app\\infrastructure\\cache\\getstats',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Cache/InMemoryCache.php' => 
    array (
      0 => 'a74646a0e78dd1f7eb0f6f6dfad12750656300d8',
      1 => 
      array (
        0 => 'app\\infrastructure\\cache\\inmemorycache',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\cache\\get',
        1 => 'app\\infrastructure\\cache\\set',
        2 => 'app\\infrastructure\\cache\\delete',
        3 => 'app\\infrastructure\\cache\\clear',
        4 => 'app\\infrastructure\\cache\\exists',
        5 => 'app\\infrastructure\\cache\\getmultiple',
        6 => 'app\\infrastructure\\cache\\setmultiple',
        7 => 'app\\infrastructure\\cache\\getstats',
        8 => 'app\\infrastructure\\cache\\cleanupexpired',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Cache/CacheInterface.php' => 
    array (
      0 => '1a78cee7b7d9bbe04226ec66a19a6fb62ea783db',
      1 => 
      array (
        0 => 'app\\infrastructure\\cache\\cacheinterface',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\cache\\get',
        1 => 'app\\infrastructure\\cache\\set',
        2 => 'app\\infrastructure\\cache\\delete',
        3 => 'app\\infrastructure\\cache\\clear',
        4 => 'app\\infrastructure\\cache\\exists',
        5 => 'app\\infrastructure\\cache\\getmultiple',
        6 => 'app\\infrastructure\\cache\\setmultiple',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Cache/CacheAction.php' => 
    array (
      0 => '77d443e89cfec917bf6527166ed080eb9b59e1a5',
      1 => 
      array (
        0 => 'app\\infrastructure\\cache\\cacheaction',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\cache\\fromstring',
        1 => 'app\\infrastructure\\cache\\getdisplayname',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Domain/Service/EventDomainService.php' => 
    array (
      0 => '7b78c12e07e7bd538caef5a7a6eb0dfaee9ff475',
      1 => 
      array (
        0 => 'app\\domain\\service\\eventdomainservice',
      ),
      2 => 
      array (
        0 => 'app\\domain\\service\\__construct',
        1 => 'app\\domain\\service\\findeventswithinradius',
        2 => 'app\\domain\\service\\findnearestevent',
        3 => 'app\\domain\\service\\iseventnameunique',
        4 => 'app\\domain\\service\\calculateeventscenter',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Domain/ValueObject/EventName.php' => 
    array (
      0 => 'b2726c396913832625e55383c58f60a34390ce09',
      1 => 
      array (
        0 => 'app\\domain\\valueobject\\eventname',
      ),
      2 => 
      array (
        0 => 'app\\domain\\valueobject\\__construct',
        1 => 'app\\domain\\valueobject\\getvalue',
        2 => 'app\\domain\\valueobject\\equals',
        3 => 'app\\domain\\valueobject\\__tostring',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Domain/ValueObject/EventId.php' => 
    array (
      0 => '07fa88fa300afa48a9785dae4039dd67fd9ee4fa',
      1 => 
      array (
        0 => 'app\\domain\\valueobject\\eventid',
      ),
      2 => 
      array (
        0 => 'app\\domain\\valueobject\\__construct',
        1 => 'app\\domain\\valueobject\\getvalue',
        2 => 'app\\domain\\valueobject\\equals',
        3 => 'app\\domain\\valueobject\\__tostring',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Domain/ValueObject/Location.php' => 
    array (
      0 => 'e4dc693db95b5ea899dac0da75a5609ba478711c',
      1 => 
      array (
        0 => 'app\\domain\\valueobject\\location',
      ),
      2 => 
      array (
        0 => 'app\\domain\\valueobject\\__construct',
        1 => 'app\\domain\\valueobject\\getvalue',
        2 => 'app\\domain\\valueobject\\equals',
        3 => 'app\\domain\\valueobject\\__tostring',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Domain/ValueObject/Coordinates.php' => 
    array (
      0 => '05fd4114383b265e56e55f8640e51fd672e5888e',
      1 => 
      array (
        0 => 'app\\domain\\valueobject\\coordinates',
      ),
      2 => 
      array (
        0 => 'app\\domain\\valueobject\\__construct',
        1 => 'app\\domain\\valueobject\\getlatitude',
        2 => 'app\\domain\\valueobject\\getlongitude',
        3 => 'app\\domain\\valueobject\\equals',
        4 => 'app\\domain\\valueobject\\distanceto',
        5 => 'app\\domain\\valueobject\\__tostring',
        6 => 'app\\domain\\valueobject\\toarray',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Domain/Entity/Event.php' => 
    array (
      0 => '8e098d9bde885ac6e804e6371cc597c44cd11da9',
      1 => 
      array (
        0 => 'app\\domain\\entity\\event',
      ),
      2 => 
      array (
        0 => 'app\\domain\\entity\\__construct',
        1 => 'app\\domain\\entity\\getid',
        2 => 'app\\domain\\entity\\getname',
        3 => 'app\\domain\\entity\\getlocation',
        4 => 'app\\domain\\entity\\getcoordinates',
        5 => 'app\\domain\\entity\\getcreatedat',
        6 => 'app\\domain\\entity\\getupdatedat',
        7 => 'app\\domain\\entity\\distanceto',
        8 => 'app\\domain\\entity\\equals',
        9 => 'app\\domain\\entity\\toarray',
        10 => 'app\\domain\\entity\\__sleep',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Domain/Repository/EventRepositoryInterface.php' => 
    array (
      0 => '030a62d6cefa88ad84ae1225cb3a4cd72487e3e7',
      1 => 
      array (
        0 => 'app\\domain\\repository\\eventrepositoryinterface',
      ),
      2 => 
      array (
        0 => 'app\\domain\\repository\\findall',
        1 => 'app\\domain\\repository\\findpaginated',
        2 => 'app\\domain\\repository\\search',
        3 => 'app\\domain\\repository\\countsearch',
        4 => 'app\\domain\\repository\\findbyid',
        5 => 'app\\domain\\repository\\count',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/Bootstrap.php' => 
    array (
      0 => '1abcb2213f3858efd84540a9fecbe6e941b22523',
      1 => 
      array (
        0 => 'app\\application\\bootstrap',
      ),
      2 => 
      array (
        0 => 'app\\application\\__construct',
        1 => 'app\\application\\run',
        2 => 'app\\application\\getrequestheaders',
        3 => 'app\\application\\setuperrorreporting',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/Service/EventService.php' => 
    array (
      0 => '6082e7978da4979b4659b1989113670dce08a931',
      1 => 
      array (
        0 => 'app\\application\\service\\eventservice',
      ),
      2 => 
      array (
        0 => 'app\\application\\service\\__construct',
        1 => 'app\\application\\service\\getallevents',
        2 => 'app\\application\\service\\geteventbyid',
        3 => 'app\\application\\service\\geteventcount',
        4 => 'app\\application\\service\\eventexists',
        5 => 'app\\application\\service\\validateeventid',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/Service/EventServiceInterface.php' => 
    array (
      0 => '7064a0fc19a442a4fce0caa65ba6448277eb8301',
      1 => 
      array (
        0 => 'app\\application\\service\\eventserviceinterface',
      ),
      2 => 
      array (
        0 => 'app\\application\\service\\getallevents',
        1 => 'app\\application\\service\\geteventbyid',
        2 => 'app\\application\\service\\geteventcount',
        3 => 'app\\application\\service\\eventexists',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Service/Providers/PresentationServiceProvider.php' => 
    array (
      0 => '6f90ed0e059ce45cf5173008e0b1c3d9475d460f',
      1 => 
      array (
        0 => 'app\\service\\providers\\presentationserviceprovider',
      ),
      2 => 
      array (
        0 => 'app\\service\\providers\\register',
        1 => 'app\\service\\providers\\registercontrollers',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Service/Providers/InfrastructureServiceProvider.php' => 
    array (
      0 => '4a3b30607186d4e6c88fc81a7c86ec6f0721ae61',
      1 => 
      array (
        0 => 'app\\service\\providers\\infrastructureserviceprovider',
      ),
      2 => 
      array (
        0 => 'app\\service\\providers\\register',
        1 => 'app\\service\\providers\\loadenvironmentconfig',
        2 => 'app\\service\\providers\\registerlogging',
        3 => 'app\\service\\providers\\registercache',
        4 => 'app\\service\\providers\\registerdatabase',
        5 => 'app\\service\\providers\\registerrepositories',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Service/Providers/ApplicationServiceProvider.php' => 
    array (
      0 => 'ea7adf147b65f4ca12b6431cd4a00f7bd9169977',
      1 => 
      array (
        0 => 'app\\service\\providers\\applicationserviceprovider',
      ),
      2 => 
      array (
        0 => 'app\\service\\providers\\register',
        1 => 'app\\service\\providers\\registerservices',
        2 => 'app\\service\\providers\\registervalidators',
        3 => 'app\\service\\providers\\registerusecases',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Service/ServiceProvider.php' => 
    array (
      0 => '7eddfdb9f888de1ee247b4c45367a6bcfaf7885a',
      1 => 
      array (
        0 => 'app\\service\\serviceprovider',
      ),
      2 => 
      array (
        0 => 'app\\service\\register',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/DataSource/DataSourceStrategy.php' => 
    array (
      0 => '0ce53083e90d7dd34130a9bad2b4a4a4db6b95f7',
      1 => 
      array (
        0 => 'app\\infrastructure\\datasource\\datasourcestrategy',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\datasource\\fromstring',
        1 => 'app\\infrastructure\\datasource\\getdisplayname',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/DataSource/DataSourceFactory.php' => 
    array (
      0 => '27aecee986912362f6391e4e4b0e2f706ed04f79',
      1 => 
      array (
        0 => 'app\\infrastructure\\datasource\\datasourcefactory',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\datasource\\__construct',
        1 => 'app\\infrastructure\\datasource\\createrepository',
        2 => 'app\\infrastructure\\datasource\\getstrategy',
        3 => 'app\\infrastructure\\datasource\\getstrategydescription',
        4 => 'app\\infrastructure\\datasource\\createdatabasefirstrepository',
        5 => 'app\\infrastructure\\datasource\\createcsvfirstrepository',
        6 => 'app\\infrastructure\\datasource\\createdatabaseonlyrepository',
        7 => 'app\\infrastructure\\datasource\\createcsvonlyrepository',
        8 => 'app\\infrastructure\\datasource\\createautorepository',
        9 => 'app\\infrastructure\\datasource\\getcsvpath',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Response/JsonResponseFormatter.php' => 
    array (
      0 => '15dc8c66941abcafa376d262405804c6ac82d280',
      1 => 
      array (
        0 => 'app\\infrastructure\\response\\jsonresponseformatter',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\response\\formatsuccess',
        1 => 'app\\infrastructure\\response\\formaterror',
        2 => 'app\\infrastructure\\response\\getcontenttype',
        3 => 'app\\infrastructure\\response\\getheaders',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Response/HtmlResponseFormatter.php' => 
    array (
      0 => 'fb03f393be9fd508ecf7110a1c116d0d633d41a5',
      1 => 
      array (
        0 => 'app\\infrastructure\\response\\htmlresponseformatter',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\response\\formatsuccess',
        1 => 'app\\infrastructure\\response\\formaterror',
        2 => 'app\\infrastructure\\response\\getcontenttype',
        3 => 'app\\infrastructure\\response\\getheaders',
        4 => 'app\\infrastructure\\response\\createhtmlpage',
        5 => 'app\\infrastructure\\response\\createeventtable',
        6 => 'app\\infrastructure\\response\\createeventdetail',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Response/ResponseFormatStrategy.php' => 
    array (
      0 => '328a1fe57b809db143de2ecfc2d5b978fe1a8301',
      1 => 
      array (
        0 => 'app\\infrastructure\\response\\responseformatstrategy',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\response\\fromstring',
        1 => 'app\\infrastructure\\response\\fromacceptheader',
        2 => 'app\\infrastructure\\response\\getdisplayname',
        3 => 'app\\infrastructure\\response\\getfileextension',
        4 => 'app\\infrastructure\\response\\createformatter',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Response/ResponseManager.php' => 
    array (
      0 => 'f94823f5d09824cb9922d0fa35d31b32eb5818d7',
      1 => 
      array (
        0 => 'app\\infrastructure\\response\\responsemanager',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\response\\__construct',
        1 => 'app\\infrastructure\\response\\sendsuccess',
        2 => 'app\\infrastructure\\response\\senderror',
        3 => 'app\\infrastructure\\response\\sendnotfound',
        4 => 'app\\infrastructure\\response\\getstrategy',
        5 => 'app\\infrastructure\\response\\getformatter',
        6 => 'app\\infrastructure\\response\\createfromrequest',
        7 => 'app\\infrastructure\\response\\createwithformat',
        8 => 'app\\infrastructure\\response\\detectformat',
        9 => 'app\\infrastructure\\response\\sendresponse',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Response/XmlResponseFormatter.php' => 
    array (
      0 => '9e72730c2607ec7803131e181e84edb7bd95df76',
      1 => 
      array (
        0 => 'app\\infrastructure\\response\\xmlresponseformatter',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\response\\formatsuccess',
        1 => 'app\\infrastructure\\response\\formaterror',
        2 => 'app\\infrastructure\\response\\getcontenttype',
        3 => 'app\\infrastructure\\response\\getheaders',
        4 => 'app\\infrastructure\\response\\arraytoxml',
        5 => 'app\\infrastructure\\response\\addarraytoxml',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Response/ResponseFormatterInterface.php' => 
    array (
      0 => '3198ade8c27124042417d35a96bdd1358b34d677',
      1 => 
      array (
        0 => 'app\\infrastructure\\response\\responseformatterinterface',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\response\\formatsuccess',
        1 => 'app\\infrastructure\\response\\formaterror',
        2 => 'app\\infrastructure\\response\\getcontenttype',
        3 => 'app\\infrastructure\\response\\getheaders',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Response/CsvResponseFormatter.php' => 
    array (
      0 => 'aa078aa836342e75f30e67441dcadfab61edb821',
      1 => 
      array (
        0 => 'app\\infrastructure\\response\\csvresponseformatter',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\response\\formatsuccess',
        1 => 'app\\infrastructure\\response\\formaterror',
        2 => 'app\\infrastructure\\response\\getcontenttype',
        3 => 'app\\infrastructure\\response\\getheaders',
        4 => 'app\\infrastructure\\response\\arraytocsv',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Cache/CacheFactory.php' => 
    array (
      0 => '4cbace89e4d92e5366fcd12f81bdf541974ac0d8',
      1 => 
      array (
        0 => 'app\\infrastructure\\cache\\cachefactory',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\cache\\createfromstrategy',
        1 => 'app\\infrastructure\\cache\\createfromenvironment',
        2 => 'app\\infrastructure\\cache\\createrediscache',
        3 => 'app\\infrastructure\\cache\\createautocache',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Logging/SpecializedLogger.php' => 
    array (
      0 => 'e2d50ea0f1383e5e7dced091c6804c62bb1ea4d3',
      1 => 
      array (
        0 => 'app\\infrastructure\\logging\\specializedlogger',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\logging\\__construct',
        1 => 'app\\infrastructure\\logging\\logdatabaseoperation',
        2 => 'app\\infrastructure\\logging\\logcacheoperation',
        3 => 'app\\infrastructure\\logging\\logsecurityevent',
        4 => 'app\\infrastructure\\logging\\logapplicationevent',
        5 => 'app\\infrastructure\\logging\\logperformance',
        6 => 'app\\infrastructure\\logging\\logbusinessevent',
        7 => 'app\\infrastructure\\logging\\logrequest',
        8 => 'app\\infrastructure\\logging\\logresponse',
        9 => 'app\\infrastructure\\logging\\emergency',
        10 => 'app\\infrastructure\\logging\\alert',
        11 => 'app\\infrastructure\\logging\\critical',
        12 => 'app\\infrastructure\\logging\\error',
        13 => 'app\\infrastructure\\logging\\warning',
        14 => 'app\\infrastructure\\logging\\notice',
        15 => 'app\\infrastructure\\logging\\info',
        16 => 'app\\infrastructure\\logging\\debug',
        17 => 'app\\infrastructure\\logging\\log',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Logging/LoggerFactory.php' => 
    array (
      0 => '9ba22121218408d0e9c75070ca5dc423089017d0',
      1 => 
      array (
        0 => 'app\\infrastructure\\logging\\loggerfactory',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\logging\\createfromenvironment',
        1 => 'app\\infrastructure\\logging\\createlogger',
        2 => 'app\\infrastructure\\logging\\createapplicationlogger',
        3 => 'app\\infrastructure\\logging\\createdatabaselogger',
        4 => 'app\\infrastructure\\logging\\createcachelogger',
        5 => 'app\\infrastructure\\logging\\createsecuritylogger',
        6 => 'app\\infrastructure\\logging\\createerrorlogger',
        7 => 'app\\infrastructure\\logging\\createperformancelogger',
        8 => 'app\\infrastructure\\logging\\createrequestlogger',
        9 => 'app\\infrastructure\\logging\\createfilehandler',
        10 => 'app\\infrastructure\\logging\\createrotatingfilehandler',
        11 => 'app\\infrastructure\\logging\\createstdouthandler',
        12 => 'app\\infrastructure\\logging\\createstderrhandler',
        13 => 'app\\infrastructure\\logging\\createnullhandler',
        14 => 'app\\infrastructure\\logging\\createformatter',
        15 => 'app\\infrastructure\\logging\\getloglevelfromenvironment',
        16 => 'app\\infrastructure\\logging\\ensurelogdirectoryexists',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Logging/ApplicationLogger.php' => 
    array (
      0 => '800b6c43e38398333a227debc9f7acb786f5e051',
      1 => 
      array (
        0 => 'app\\infrastructure\\logging\\applicationlogger',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\logging\\__construct',
        1 => 'app\\infrastructure\\logging\\logdatabaseoperation',
        2 => 'app\\infrastructure\\logging\\logcacheoperation',
        3 => 'app\\infrastructure\\logging\\logsecurityevent',
        4 => 'app\\infrastructure\\logging\\logapplicationevent',
        5 => 'app\\infrastructure\\logging\\logperformance',
        6 => 'app\\infrastructure\\logging\\logbusinessevent',
        7 => 'app\\infrastructure\\logging\\logrequest',
        8 => 'app\\infrastructure\\logging\\logresponse',
        9 => 'app\\infrastructure\\logging\\emergency',
        10 => 'app\\infrastructure\\logging\\alert',
        11 => 'app\\infrastructure\\logging\\critical',
        12 => 'app\\infrastructure\\logging\\error',
        13 => 'app\\infrastructure\\logging\\warning',
        14 => 'app\\infrastructure\\logging\\notice',
        15 => 'app\\infrastructure\\logging\\info',
        16 => 'app\\infrastructure\\logging\\debug',
        17 => 'app\\infrastructure\\logging\\log',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Logging/NullLogger.php' => 
    array (
      0 => '80b8533227f61178403df95b02564e2f513a8d86',
      1 => 
      array (
        0 => 'app\\infrastructure\\logging\\nulllogger',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\logging\\logdatabaseoperation',
        1 => 'app\\infrastructure\\logging\\logcacheoperation',
        2 => 'app\\infrastructure\\logging\\logsecurityevent',
        3 => 'app\\infrastructure\\logging\\logapplicationevent',
        4 => 'app\\infrastructure\\logging\\logperformance',
        5 => 'app\\infrastructure\\logging\\logbusinessevent',
        6 => 'app\\infrastructure\\logging\\logrequest',
        7 => 'app\\infrastructure\\logging\\logresponse',
        8 => 'app\\infrastructure\\logging\\emergency',
        9 => 'app\\infrastructure\\logging\\alert',
        10 => 'app\\infrastructure\\logging\\critical',
        11 => 'app\\infrastructure\\logging\\error',
        12 => 'app\\infrastructure\\logging\\warning',
        13 => 'app\\infrastructure\\logging\\notice',
        14 => 'app\\infrastructure\\logging\\info',
        15 => 'app\\infrastructure\\logging\\debug',
        16 => 'app\\infrastructure\\logging\\log',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Logging/LoggerInterface.php' => 
    array (
      0 => 'a71e34c04a42cc98d4cdcd09233bb709cbf7c5e8',
      1 => 
      array (
        0 => 'app\\infrastructure\\logging\\loggerinterface',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\logging\\logdatabaseoperation',
        1 => 'app\\infrastructure\\logging\\logcacheoperation',
        2 => 'app\\infrastructure\\logging\\logsecurityevent',
        3 => 'app\\infrastructure\\logging\\logapplicationevent',
        4 => 'app\\infrastructure\\logging\\logperformance',
        5 => 'app\\infrastructure\\logging\\logbusinessevent',
        6 => 'app\\infrastructure\\logging\\logrequest',
        7 => 'app\\infrastructure\\logging\\logresponse',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Validation/EventIdValidator.php' => 
    array (
      0 => 'a98132c156e80f984741bf7fd9a7e40a4a69ccd4',
      1 => 
      array (
        0 => 'app\\infrastructure\\validation\\eventidvalidator',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\validation\\validate',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Validation/ValidatorInterface.php' => 
    array (
      0 => '5270208a225e92636dbdb10238d186fccf6b1282',
      1 => 
      array (
        0 => 'app\\infrastructure\\validation\\validatorinterface',
        1 => 'app\\infrastructure\\validation\\validationresult',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\validation\\validate',
        1 => 'app\\infrastructure\\validation\\__construct',
        2 => 'app\\infrastructure\\validation\\isvalid',
        3 => 'app\\infrastructure\\validation\\geterrors',
        4 => 'app\\infrastructure\\validation\\getfirsterror',
        5 => 'app\\infrastructure\\validation\\success',
        6 => 'app\\infrastructure\\validation\\failure',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Infrastructure/Validation/PaginationValidator.php' => 
    array (
      0 => 'edd137e8ce27de561b94c7df28fd831b32711e20',
      1 => 
      array (
        0 => 'app\\infrastructure\\validation\\paginationvalidator',
      ),
      2 => 
      array (
        0 => 'app\\infrastructure\\validation\\validate',
        1 => 'app\\infrastructure\\validation\\validatepage',
        2 => 'app\\infrastructure\\validation\\validatepagesize',
        3 => 'app\\infrastructure\\validation\\validatesortby',
        4 => 'app\\infrastructure\\validation\\validatesortdirection',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Router/RouteProvider.php' => 
    array (
      0 => '1d1ee8d02125a2ea6989d63ef66050a29ee153e1',
      1 => 
      array (
        0 => 'app\\router\\routeprovider',
      ),
      2 => 
      array (
        0 => 'app\\router\\registerroutes',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/Service/SearchServiceInterface.php' => 
    array (
      0 => '88a4ba8174d43a751391dceeca885e1f3cd93a2b',
      1 => 
      array (
        0 => 'app\\application\\service\\searchserviceinterface',
      ),
      2 => 
      array (
        0 => 'app\\application\\service\\searchevents',
        1 => 'app\\application\\service\\getsearchsuggestions',
        2 => 'app\\application\\service\\getpopularsearchterms',
        3 => 'app\\application\\service\\geteventsnearby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/Service/SearchService.php' => 
    array (
      0 => 'ae72b84c90d5194c9d7f39202ecbfa46e805bfab',
      1 => 
      array (
        0 => 'app\\application\\service\\searchservice',
      ),
      2 => 
      array (
        0 => 'app\\application\\service\\__construct',
        1 => 'app\\application\\service\\searchevents',
        2 => 'app\\application\\service\\getsearchsuggestions',
        3 => 'app\\application\\service\\getpopularsearchterms',
        4 => 'app\\application\\service\\geteventsnearby',
      ),
      3 => 
      array (
      ),
    ),
    '/var/www/src/Application/Query/SearchQuery.php' => 
    array (
      0 => '867099abebb02450e52181b4a3d425b9810357dd',
      1 => 
      array (
        0 => 'app\\application\\query\\searchquery',
      ),
      2 => 
      array (
        0 => 'app\\application\\query\\__construct',
        1 => 'app\\application\\query\\hassearch',
        2 => 'app\\application\\query\\haslocationfilter',
        3 => 'app\\application\\query\\hasgeographicsearch',
        4 => 'app\\application\\query\\hasdatefilter',
        5 => 'app\\application\\query\\hasanyfilter',
        6 => 'app\\application\\query\\getcachekey',
        7 => 'app\\application\\query\\validatesearchparameters',
        8 => 'app\\application\\query\\isvaliddate',
      ),
      3 => 
      array (
      ),
    ),
  ),
));