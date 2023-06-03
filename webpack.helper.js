const babel = require('@babel/core');
const uglifyJS = require('uglify-js');

const scriptsGroups = (APP_DIR) => {
    return [
        {
            dest: 'scriptsWebVendor.bundle.js',
            list: [
                APP_DIR + "/node_modules/jquery/dist/jquery.min.js",
                APP_DIR + "/node_modules/jquery-ui-sortable/jquery-ui.min.js",
                APP_DIR + "/www/libs/fontawesome/js/all.min.js",
                APP_DIR + "/node_modules/sweetalert2/dist/sweetalert2.min.js",
                APP_DIR + "/node_modules/bootstrap/dist/js/bootstrap.min.js",
                APP_DIR + "/node_modules/izitoast/dist/js/iziToast.min.js",
                APP_DIR + "/node_modules/daterangepicker/daterangepicker.js",
                APP_DIR + "/node_modules/nette.ajax.js/nette.ajax.js",
                APP_DIR + "/node_modules/naja/dist/Naja.min.js",
                APP_DIR + "/node_modules/live-form-validation/live-form-validation.js",
                APP_DIR + "/node_modules/nette.ajax.js/extensions/spinner.ajax.js",
                APP_DIR + "/www/js/web/votes.js",
                APP_DIR + '/www/js/swal.js'
                // scripty definované zde by se měly objevit i ve webpack.config.js v sekci "externals"
            ]
        },
        {
            dest: 'scriptsAdmin.bundle.js',
            list: [
                APP_DIR + "/node_modules/jquery/dist/jquery.min.js",
                APP_DIR + "/node_modules/jquery-ui-sortable/jquery-ui.min.js",
                APP_DIR + "/www/libs/fontawesome/js/all.min.js",
                APP_DIR + "/node_modules/bootstrap/dist/js/bootstrap.min.js",
                APP_DIR + "/node_modules/sweetalert2/dist/sweetalert2.min.js",
                APP_DIR + "/node_modules/izitoast/dist/js/iziToast.min.js",
                APP_DIR + "/node_modules/daterangepicker/daterangepicker.js",
                APP_DIR + "/node_modules/nette.ajax.js/nette.ajax.js",
                APP_DIR + "/node_modules/naja/dist/Naja.min.js",
                APP_DIR + "/node_modules/live-form-validation/live-form-validation.js",
                APP_DIR + "/node_modules/nette.ajax.js/extensions/spinner.ajax.js",
                APP_DIR + "/node_modules/ajax-bootstrap-select/dist/js/ajax-bootstrap-select.js",
                APP_DIR + "/node_modules/ublaboo-datagrid/assets/datagrid.js",
                APP_DIR + "/node_modules/ublaboo-datagrid/assets/datagrid-spinners.js",
                APP_DIR + "/node_modules/ublaboo-datagrid/assets/datagrid-instant-url-refresh.js",
                APP_DIR + "/node_modules/moment/moment.js",
                APP_DIR + "/node_modules/daterangepicker/daterangepicker.js",
                APP_DIR + "/node_modules/easymde/dist/easymde.min.js",
                APP_DIR + "/www/js/admin/admin.js",
                APP_DIR + "/www/js/admin/tabs.js",
                APP_DIR + "/www/js/admin/markdown-editor.js",
                APP_DIR + '/www/js/swal.js'
            ]
        }
    ];
}

const generateArrayForMerge = (APP_DIR, isProduction) => {

    const output = [];

    scriptsGroups(APP_DIR).forEach(item => {
        output.push(
            {
                src: item.list,
                dest: code => handleScriptDest(code, isProduction, item.dest)
            }
        );
    });

    return output;
}

// minifikace, transpilace, estinace bundlu

const handleScriptDest = (code, isProduction, name, useBabel) => {

    // transpilace babelem (const, let, arrow funkce, ...)

    const transpiled = useBabel ? babel.transformSync(code, {
        "presets": [
            [
                "@babel/preset-env",
            ]
        ]
    }).code : code;

    // minifikace v produkčním režimu

    if (isProduction) {
        const min = uglifyJS.minify(transpiled, {
            compress: {
                inline: 1,
            },
        });

        return {
            [name]: min.code,
        };
    }

    // vrácení kódu (včetně specifikace destinace)

    return {
        [name]: transpiled,
    };

};

module.exports = { generateArrayForMerge };
