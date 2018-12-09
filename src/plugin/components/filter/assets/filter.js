function gdFilterSubmit(formId, href) {

    var parser = document.createElement('a');

    parser.href = href;

    var params = new URLSearchParams(parser.search);

    var form = document.getElementById(formId);

    var input = form.getElementsByTagName('input');

    var select = form.getElementsByTagName('select');

    var textarea = form.getElementsByTagName('textarea');

    var elements = Array.prototype.concat.call(input, select, textarea);

    for (var i = 0; i < elements.length; ++i)
    {
        for (var ii = 0; ii < elements[i].length; ++ii)
        {
            var value = elements[i][ii].value;

            var name = elements[i][ii].name;

            if (value === '' || name === '')

                continue;

            params.set(name, value);
        }
    }

    window.location.href = parser.pathname + '?' + params.toString()
}