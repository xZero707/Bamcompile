# Bamcompile
Bambalam PHP EXE Compiler/Embedder

> This project was abandoned long time ago by it's original author
> Anders Hammar. Latest official update was on 28th of August 2006. My
> plans are to implement PHP5 support instead of obsolete PHP4 and
> improve this software as well.
> 
> I believe that this software deserves second chance. 
> I have tried many PHP "compilers", but this one was most stable and easy to use. 

 1. **What is it?**
 Bambalam PHP EXE Compiler/Embedder is a free command line tool to convert PHP applications to standalone Windows .exe applications. The exe files produced are totally standalone, no need for php dlls etc. The php code is encoded using the [Turck MMCache Encode library](http://turck-mmcache.sourceforge.net/) so it's a perfect solution if you want to distribute your application while protecting your source code. The converter is also suitable for producing .exe files for windowed PHP applications (created using for example the [WinBinder library](http://www.winbinder.com/)). It's also good for making stand-alone PHP Socket servers/clients (using the php_sockets.dll extension). 
It's NOT really a compiler in the sense that it doesn't produce native machine code from PHP sources, but it works! 
The tool is free and open source. However, if you use it commercially (ie to make money in some way) you should make a [donation](http://sourceforge.net/donate/index.php?group_id=169996) to keep the project alive! 

 2. **How does it work?**
 The converter embeds encoded PHP source files as resources in a generic statically compiled PHP launcher executable. It also has an option to compress the final exe using the [UPX executable compressor](http://upx.sourceforge.net/). Simple console applications end up with an executable at a size of 500-600kb. 
 3. **Does "compiled" PHP applications run faster?**
 Theoretically it should. Having libraries linked statically and encoding files (Turck MMCache encodes PHP sources as PHP bytecode, I think) should contribute to faster applications. But I haven't done any decent tests on it. 
 
 4. **What about PHP applications with more than one source file?**
 No problem, the converter is able to embed a whole project directory. Or, if you want, you can create a bamcompile project file (.bcp) and embed files from all over the place. 
 
 5. **What about accessing the embedded files from php?**
 You can access the embedded files just as you're used to. The PHP runtime used has been modified so that when you're accessing a file it first tries to access it the usual way (by looking in the path outside the .exe) and if the files aren't found there it looks for it in the embedded filelist.
 
 6. **So the INCLUDE statement works just as it should?**
 Yes, as long as you include stuff using relative paths. 
 
 7. **What PHP version is supported?**
 Currently, the converter uses a PHP runtime based on PHP 4.4.4
 Plans are to implement PHP5 and above.
 
 8. **What about PHP 5 support?**
 I haven't got around to it, but I probably will! But PHP 4.4.4 works in most cases, and the PHP 4 runtimes produces alot smaller standalone exe files than PHP 5 would do. PHP 5 is a bitch to build on windows (well, it's easy to build the standard builds, but other than that..) But as I said, just wait for it. 


 9. **What libraries / extensions are included?**
As of version 1.2, php libraries (or extensions) now can be embedded as needed. So, for example, if you're making an application that uses the gd extension, use the -e:php_gd2.dll option and you'll be fine. You can also use the EXTENSION command when working with bamcompile project files. You still have to have access to the php extension dll files when embedding of course, they are shipped together with the PHP distribution. But once you have compiled your exe, that's the only file you have to distribute! 
Note however that some extensions use other system dlls which needs to be shipped together with your compiled exe to make it work.
Some examples:
> php_curl.dll needs libeay32.dll and ssleay32.dll 
> php_mssql.dll needs ntwdblib.dll 
> php_fribidi.dll needs fribidi.dll 
> php_fdf.dll needs FDFTK.dll 
> php_ifx.dll needs isqlt09a.dll 
> php_mhash.dll needs ibmhash.dll 
> php_msql.dll needs mSQL.dll

 But most of them, such as GD, Sockets, Winbinder and Sqlite don't need any system dlls and they embed just fine.
 
 The following libraries are included statically:
> Turck
> MMCache 
> bcmath 
> calendar 
> com 
> ctype
> ftp
> mysql
> odbc
> pcre
> win32std
> xml zlib


 10. **Can I add a custom Icon to my exe?**
 Yes, use the -i:icon.ico command line option or the ICON project file command. 
 11. What about PHP.INI? Can I embed that as well?
 Yes, just include the php.ini in the root of your project. However, if you want to use extensions, use the internal extension loader instead. Older versions of Bamcompile used the embedded php.ini to load extensions but it was messy and honestly didn't work that well. You can still embed php.ini to set ini specific settings.
 
 12. **What is the format of Bamcompile Project files?**
 A project file has the extension .bcp and is built up using simple commands, one on each line:

 MAINFILE mainfile.php
Sets the mainfile to whatever mainfile you want to use. 

 OUTFILE outfile.exe
Sets the output file exe name. Optional.

 ICON icon.ico
Sets an icon for the output exe file. Optional.

 COMPRESS
Compresses final exe file with UPX

 DONTENCODE
Do not encode php files

 WINDOWED
Windowed application, removes the "dos box" otherwise shown

 EMBED directory/file.php
 EMBED whole_directory
 EMBED directory/*.png
Used to embed files. You can embed single files, directories or files using a filters. Directories are embedded recursivly

 DESTINATION destination_path/
Sets the destination path in the embedded filesystem. Use the destination command before an embed command to change where the embedded files end up. The default destination path is, of course, the root /.

 EXTENSION path_to/extension.dll
Adds a PHP extension to the internal extension loader. The extension will be embedded and loaded from memory. 

 13. **License**
 Take a look at **LICENSE.MD**


*Usage:*
   bamcompile [-options] infile.php [outfile.exe]
   bamcompile [-options] project_directory mainfile.php [outfile.exe]
   bamcompile projectfile.bcp

**Options:**
    -w Hide console window for windowed applications
    -c Compress output exe (using UPX)
    -d Do not encode PHP files
    -e:extension.dll Embed and use PHP extension
    -i:icon.ico Add icon to exe
