<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



# LMB_DBTYPE #

const LMB_DBTYPE_CHAR = 'CHAR';
const LMB_DBTYPE_VARCHAR = 'VARCHAR';
const LMB_DBTYPE_INTEGER = 'INTEGER';
const LMB_DBTYPE_SMALLINT = 'SMALLINT';
const LMB_DBTYPE_BIGINT = 'BIGINT';
const LMB_DBTYPE_FLOAT = 'BINARY_FLOAT';
const LMB_DBTYPE_FIXED = 'NUMBER';
const LMB_DBTYPE_NUMERIC = 'NUMBER';
const LMB_DBTYPE_BOOLEAN = 'NUMBER(1)';
const LMB_DBTYPE_DATE = 'DATE';
const LMB_DBTYPE_TIME = 'DATE';#-------------------------------------------------------Zeit ist in date enthalten
const LMB_DBTYPE_TIMESTAMP = 'TIMESTAMP';
const LMB_DBTYPE_LONG = 'CLOB';
const LMB_DBTYPE_ASCII = '';
const LMB_DBTYPE_BYTE = '';

# LMB_DBDEF #Default
const LMB_DBDEF_TIME = 'SYSDATE';#NLS_TIMESTAMP_FORMAT ist default wert zu setzen
const LMB_DBDEF_DATE = 'SYSDATE';
const LMB_DBDEF_TIMESTAMP = 'SYSTIMESTAMP';
const LMB_DBDEF_FALSE = '0';
const LMB_DBDEF_TRUE = '1';
const LMB_DBDEF_NULL = 'NULL';

# LMB_DBRETYPE #
const LMB_DBRETYPE_CHAR = 'CHAR';
const LMB_DBRETYPE_VARCHAR = 'VARCHAR';
const LMB_DBRETYPE_VARCHAR2 = 'VARCHAR';
#const LMB_DBRETYPE_NVARCHAR2 = 'VARCHAR';
const LMB_DBRETYPE_INTEGER = 'INTEGER';
const LMB_DBRETYPE_SMALLINT = 'SMALLINT';
const LMB_DBRETYPE_FLOAT = 'FLOAT';
#const LMB_DBRETYPE_FIXED = 'FIXED';
const LMB_DBRETYPE_NUMERIC = 'NUMERIC';
#const LMB_DBRETYPE_BOOLEAN = 'BOOLEAN';
const LMB_DBRETYPE_DATE = 'DATE';
#const LMB_DBRETYPE_TIME = 'TIME';
const LMB_DBRETYPE_TIMESTAMP = 'TIMESTAMP';
const LMB_DBRETYPE_CLOB = 'LONG';

# LMB_DBREDEF #
const LMB_DBREDEF_TIMESTAMP = 'TIMESTAMP';

# LMB_DBFUNC #
const LMB_DBFUNC_ISNULL = 'IS NULL';
const LMB_DBFUNC_PRIMARY_KEY = 'PRIMARY KEY';
const LMB_DBFUNC_UNIQUE = 'UNIQUE';
const LMB_DBFUNC_CONCAT = '.';
const LMB_DBFUNC_UMASCB = '\\\\';
const LMB_DBFUNC_LIMIT = '';#abzuklären
const LMB_DBFUNC_ROWNO = 'ROWNUM';
const LMB_DBFUNC_SPLIT_TRIGGER = 'EXECUTE';#Ausführung eines Triggers
const LMB_DBFUNC_ADD_COLUMN_FIRST = 'ADD';#alter table TABELLE add testZahl number not null;
const LMB_DBFUNC_ADD_COLUMN_NEXT = '';#alter table TABELLE add (testZahl number not null, testString varchar2(10));
const LMB_DBFUNC_DROP_COLUMN_FIRST = 'DROP';#alter table TABELLE drop column testZahl;
const LMB_DBFUNC_DROP_COLUMN_NEXT = '';#alter table TABELLE drop (testZahl, testNummer);
const LMB_DBFUNC_DATE = 'DATE(';#Parameter ist Feld Timestamp oder Date
const LMB_DBFUNC_TIME = 'TIME(';#select to_char(sysdate, 'hh24:MI:SS') from dual
const LMB_DBFUNC_YEAR = 'YEAR(';#select to_char(sysdate, 'yyyy') from dual
const LMB_DBFUNC_MONTH = 'MONTH(';#select to_char(sysdate, 'mm') from dual
const LMB_DBFUNC_DAY = 'DAY(';#select to_char(sysdate, 'dd') from dual
const LMB_DBFUNC_HOUR = 'HOUR(';#select to_char(sysdate, 'hh24') from dual
const LMB_DBFUNC_MINUTE = 'MINUTE(';#select to_char(sysdate, 'MI') from dual

const LMB_DBFUNC_LONGHANDLE = 1;#Abzuklären ob Einschränkungen beim Bearbeiten
const LMB_DBFUNC_PROCEDUREHANDLE = 1;
const LMB_DBFUNC_FKEYHANDLE = 1;
const LMB_DBFUNC_FLOATHANDLE = 1;
const LMB_DBFUNC_PREPAREHANDLE = 1;
const LMB_DBFUNC_TIMEHANDLE = 0;
const LMB_DBFUNC_NUMROWS = 1;
const LMB_DBFUNC_VIEWGALIAS = 0;
const LMB_DBFUNC_VIEWDEPENDENCY = 0;
const LMB_DBFUNC_MAXFIELDNAMESIZE = 30;
const LMB_DBFUNC_MAXTABLENAMESIZE = 30;
const LMB_DBFUNC_TRANSACTION = 1;
const LMB_DBFUNC_TRANSACTION_WITH_SCHEMA = 0;

# DBCURSOR #
const LMB_DBCURSOR = 1;
