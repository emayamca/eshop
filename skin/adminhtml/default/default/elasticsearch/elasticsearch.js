
var Elasticsearch = {
    servers: {
        check: function (url) {
            var data = {
                servers : $("catalog_search_elasticsearch_servers").value
            };
            new Ajax.Request(url, {
                parameters: data,
                method: "post",
                onSuccess: function (response) {

                    var data = response.responseText.evalJSON();
                    var html = "<br/>";

                    data.each(function (host_data) {
                        html += "<h3>" + host_data.host + "</h3>";
                        if (host_data.data == "false") {
                            html += "<span class='error'>ERROR</span><br/><br/>The server is not reachable";
                        } else {
                            html += "<span class='success'>SUCCESS</span><br/><br/>";
                            html += "<b>Name</b> : " + host_data.data.name + "<br/>";
                            html += "<b>Cluster name</b> : " + host_data.data.cluster_name + "<br/>";
                            html += "<b>Elasticsearch version</b> : " + host_data.data.version.number + "<br/>";
                        }
                        html += "<br/><br/>";
                    });

                    var dialogWindow = Dialog.info(null, {
                        closable: true,
                        resizable: false,
                        draggable: true,
                        className: 'magento',
                        windowClassName: 'popup-window',
                        title: 'Check servers',
                        top: 50,
                        width: 500,
                        height: 500,
                        zIndex: 1000,
                        recenterAuto: false,
                        hideEffect: Element.hide,
                        showEffect: Element.show,
                        id: 'browser_window'
                    });

                    $('modal_dialog_message').innerHTML = html;
                }
            });
        }
    }
};
