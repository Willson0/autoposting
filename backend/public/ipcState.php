<?php __HALT_COMPILER(); O:32:"danog\MadelineProto\Ipc\IpcState":3:{s:45:" danog\MadelineProto\Ipc\IpcState startupTime";d:1743973079.234231;s:43:" danog\MadelineProto\Ipc\IpcState startupId";i:1762091978;s:43:" danog\MadelineProto\Ipc\IpcState exception";O:35:"danog\MadelineProto\Ipc\ExitFailure":3:{s:41:" danog\MadelineProto\Ipc\ExitFailure type";s:26:"Amp\Ipc\IpcServerException";s:42:" danog\MadelineProto\Ipc\ExitFailure props";a:2:{s:26:"Amp\Ipc\IpcServerException";a:4:{s:7:"message";s:1207:"Could not create IPC server: TCP: (errno: 0) exception: \danog\MadelineProto\Exception: stream_socket_server(): Unable to connect to tcp://127.0.0.1:0 (Операция успешно завершена) in C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\ipc\lib\IpcServer.php:105
Revision: 8.4.1
TL Trace:

exceptionErrorHandler(2,"stream_socket_server(): Unable to connect to tcp:\/\/127.0.0.1:0 (\u041e\u043f\u0435\u0440\u0430\u0446\u0438\u044f \u0443\u0441\u043f\u0435\u0448\u043d\u043e \u0437\u0430\u0432\u0435\u0440\u0448\u0435\u043d\u0430)","C:\\Users\\ghgmm\\Documents\\projects\\Autoposting\\backend\\vendor\\danog\\ipc\\lib\\IpcServer.php",105)
IpcServer.php(105): 	stream_socket_server("tcp:\/\/127.0.0.1:0",0,"",12)
AbstractServer.php(99):	__construct("C:\\Users\\ghgmm\\Documents\\projects\\Autoposting\\backend\\public\\\\ipc")
MTProto.php(997):   	setIpcPath({})
MTProto.php(1132):  	cleanupProperties()
API.php(343):       	wakeup({},{})
API.php(194):       	connectToMadelineProto({})
entry.php(114):     	__construct("C:\\Users\\ghgmm\\Documents\\projects\\Autoposting\\backend\\public\\",{})
entry.php(154):     	danog\MadelineProto\Ipc\Runner\{closure}(); ";s:4:"code";i:0;s:4:"file";s:88:"C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\ipc\lib\IpcServer.php";s:4:"line";i:137;}s:9:"Exception";a:7:{s:7:"message";s:1207:"Could not create IPC server: TCP: (errno: 0) exception: \danog\MadelineProto\Exception: stream_socket_server(): Unable to connect to tcp://127.0.0.1:0 (Операция успешно завершена) in C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\ipc\lib\IpcServer.php:105
Revision: 8.4.1
TL Trace:

exceptionErrorHandler(2,"stream_socket_server(): Unable to connect to tcp:\/\/127.0.0.1:0 (\u041e\u043f\u0435\u0440\u0430\u0446\u0438\u044f \u0443\u0441\u043f\u0435\u0448\u043d\u043e \u0437\u0430\u0432\u0435\u0440\u0448\u0435\u043d\u0430)","C:\\Users\\ghgmm\\Documents\\projects\\Autoposting\\backend\\vendor\\danog\\ipc\\lib\\IpcServer.php",105)
IpcServer.php(105): 	stream_socket_server("tcp:\/\/127.0.0.1:0",0,"",12)
AbstractServer.php(99):	__construct("C:\\Users\\ghgmm\\Documents\\projects\\Autoposting\\backend\\public\\\\ipc")
MTProto.php(997):   	setIpcPath({})
MTProto.php(1132):  	cleanupProperties()
API.php(343):       	wakeup({},{})
API.php(194):       	connectToMadelineProto({})
entry.php(114):     	__construct("C:\\Users\\ghgmm\\Documents\\projects\\Autoposting\\backend\\public\\",{})
entry.php(154):     	danog\MadelineProto\Ipc\Runner\{closure}(); ";s:6:"string";s:2672:"Amp\Ipc\IpcServerException: Could not create IPC server: TCP: (errno: 0) exception: \danog\MadelineProto\Exception: stream_socket_server(): Unable to connect to tcp://127.0.0.1:0 (Операция успешно завершена) in C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\ipc\lib\IpcServer.php:105
Revision: 8.4.1
TL Trace:

exceptionErrorHandler(2,"stream_socket_server(): Unable to connect to tcp:\/\/127.0.0.1:0 (\u041e\u043f\u0435\u0440\u0430\u0446\u0438\u044f \u0443\u0441\u043f\u0435\u0448\u043d\u043e \u0437\u0430\u0432\u0435\u0440\u0448\u0435\u043d\u0430)","C:\\Users\\ghgmm\\Documents\\projects\\Autoposting\\backend\\vendor\\danog\\ipc\\lib\\IpcServer.php",105)
IpcServer.php(105): 	stream_socket_server("tcp:\/\/127.0.0.1:0",0,"",12)
AbstractServer.php(99):	__construct("C:\\Users\\ghgmm\\Documents\\projects\\Autoposting\\backend\\public\\\\ipc")
MTProto.php(997):   	setIpcPath({})
MTProto.php(1132):  	cleanupProperties()
API.php(343):       	wakeup({},{})
API.php(194):       	connectToMadelineProto({})
entry.php(114):     	__construct("C:\\Users\\ghgmm\\Documents\\projects\\Autoposting\\backend\\public\\",{})
entry.php(154):     	danog\MadelineProto\Ipc\Runner\{closure}();  in C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\ipc\lib\IpcServer.php:137
Stack trace:
#0 C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\Ipc\AbstractServer.php(99): Amp\Ipc\IpcServer->__construct('C:\\Users\\ghgmm\\...')
#1 C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\MTProto.php(997): danog\MadelineProto\Ipc\AbstractServer->setIpcPath(Object(danog\MadelineProto\SessionPaths))
#2 C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\MTProto.php(1132): danog\MadelineProto\MTProto->cleanupProperties()
#3 C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\API.php(343): danog\MadelineProto\MTProto->wakeup(Object(danog\MadelineProto\SettingsEmpty), Object(danog\MadelineProto\APIWrapper))
#4 C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\API.php(194): danog\MadelineProto\API->connectToMadelineProto(Object(danog\MadelineProto\SettingsEmpty))
#5 C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\Ipc\Runner\entry.php(114): danog\MadelineProto\API->__construct('C:\\Users\\ghgmm\\...', Object(danog\MadelineProto\Settings\Ipc))
#6 C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\Ipc\Runner\entry.php(154): danog\MadelineProto\Ipc\Runner\{closure}()
#7 {main}";s:4:"code";i:0;s:4:"file";s:88:"C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\ipc\lib\IpcServer.php";s:4:"line";i:137;s:5:"trace";a:7:{i:0;a:6:{s:4:"file";s:107:"C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\Ipc\AbstractServer.php";s:4:"line";i:99;s:8:"function";s:11:"__construct";s:5:"class";s:17:"Amp\Ipc\IpcServer";s:4:"type";s:2:"->";s:4:"args";a:1:{i:0;s:32:""C:\Users\ghgmm\Documents\pr..."";}}i:1;a:6:{s:4:"file";s:96:"C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\MTProto.php";s:4:"line";i:997;s:8:"function";s:10:"setIpcPath";s:5:"class";s:38:"danog\MadelineProto\Ipc\AbstractServer";s:4:"type";s:2:"->";s:4:"args";a:1:{i:0;s:40:"Object(danog\MadelineProto\SessionPaths)";}}i:2;a:6:{s:4:"file";s:96:"C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\MTProto.php";s:4:"line";i:1132;s:8:"function";s:17:"cleanupProperties";s:5:"class";s:27:"danog\MadelineProto\MTProto";s:4:"type";s:2:"->";s:4:"args";a:0:{}}i:3;a:6:{s:4:"file";s:92:"C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\API.php";s:4:"line";i:343;s:8:"function";s:6:"wakeup";s:5:"class";s:27:"danog\MadelineProto\MTProto";s:4:"type";s:2:"->";s:4:"args";a:2:{i:0;s:41:"Object(danog\MadelineProto\SettingsEmpty)";i:1;s:38:"Object(danog\MadelineProto\APIWrapper)";}}i:4;a:6:{s:4:"file";s:92:"C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\API.php";s:4:"line";i:194;s:8:"function";s:22:"connectToMadelineProto";s:5:"class";s:23:"danog\MadelineProto\API";s:4:"type";s:2:"->";s:4:"args";a:1:{i:0;s:41:"Object(danog\MadelineProto\SettingsEmpty)";}}i:5;a:6:{s:4:"file";s:105:"C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\Ipc\Runner\entry.php";s:4:"line";i:114;s:8:"function";s:11:"__construct";s:5:"class";s:23:"danog\MadelineProto\API";s:4:"type";s:2:"->";s:4:"args";a:2:{i:0;s:32:""C:\Users\ghgmm\Documents\pr..."";i:1;s:40:"Object(danog\MadelineProto\Settings\Ipc)";}}i:6;a:4:{s:4:"file";s:105:"C:\Users\ghgmm\Documents\projects\Autoposting\backend\vendor\danog\madelineproto\src\Ipc\Runner\entry.php";s:4:"line";i:154;s:8:"function";s:40:"danog\MadelineProto\Ipc\Runner\{closure}";s:4:"args";a:0:{}}}s:8:"previous";N;}}s:45:" danog\MadelineProto\Ipc\ExitFailure previous";N;}}