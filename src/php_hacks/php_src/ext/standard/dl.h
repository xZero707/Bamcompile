/* 
   +----------------------------------------------------------------------+
   | PHP Version 4                                                        |
   +----------------------------------------------------------------------+
   | Copyright (c) 1997-2008 The PHP Group                                |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Authors: Brian Schaffner <brian@tool.net>                            |
   |          Shane Caraveo <shane@caraveo.com>                           |
   |          Zeev Suraski <zeev@zend.com>                                |
   +----------------------------------------------------------------------+
*/

/* $Id$ */

#ifndef DL_H
#define DL_H

void php_dl(pval *file, int type,pval *return_value TSRMLS_DC);
void php_dl_memory(pval *file, zval *dlldata, int type,pval *return_value TSRMLS_DC);
void php_load_dll(pval *file, zval *dlldata, pval *return_value TSRMLS_DC);

/* dynamic loading functions */
PHP_FUNCTION(dl);
PHP_FUNCTION(dl_memory);
PHP_FUNCTION(load_dll);

//PHPAPI extern int dl_memory(char *file, char *dlldata TSRMLS_DC);


PHP_MINFO_FUNCTION(dl);
PHP_MINFO_FUNCTION(dl_memory);

#endif /* DL_H */
