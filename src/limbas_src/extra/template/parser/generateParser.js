/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/* use
 * > npm install pegjs
 * > npm install phpegjs
 * > node generateParser.js
 * in this directory to generate parser
 */

var pegjs = require("pegjs");
var phpegjs = require("phpegjs");
var fs = require("fs");

fs.readFile(__dirname + "/grammar.txt", "utf-8", function(err, grammar) {
    if (err) {
	    throw err;
    }
    var parser = pegjs.generate(grammar, {
	    plugins: [phpegjs],
        phpegjs: {parserNamespace: ''}
    });

    // fix wrong substring
    parser = parser.replace(/substr\(\$this->input, \$this->peg_reportedPos, \$this->peg_reportedPos \+ \$this->peg_currPos\)/g, 'substr(is_array($this->input) ? implode(\'\', $this->input) : $this->input, $this->peg_reportedPos, $this->peg_currPos - $this->peg_reportedPos)');

    // fix $ in ""
    parser = parser.replace(/"([^\n\s]*)\$([^\n\s]*)"/g, (match, p1, p2) => {
        return "'" + p1.replace('\"', '\'') + "$" + p2.replace('\"', '\'') + "'";
    });

    parser = parser.replace(/strlen\(/g, "lmb_strlen(");
    parser = parser.replace(/substr\(/g, "lmb_substr(");

    fs.writeFile("searchParserGenerated.php", parser, function(err) {
        if (err) {
            throw err;
        }
    });
});

