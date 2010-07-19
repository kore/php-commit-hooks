init :
	git submodule init ; \
	git submodule update
	svn co svn://pureenergy.cc/systemProcess/trunk/src/classes src/external/system_process
	svn co svn://pureenergy.cc/systemProcess/trunk/src/exceptions/systemProcess src/external/exceptions/system_process
