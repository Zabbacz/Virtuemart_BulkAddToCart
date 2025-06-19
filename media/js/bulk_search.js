function getText(event, inputId) {
    let string = event.textContent;
    string = string.replace(/"/g, "");

//    console.log("Zvolený text: " + string); // Debugging

    // Odesílání požadavku pomocí Fetch API
    fetch("index.php?option=com_ajax&module=virtuemart_zbulkaddtocart&format=raw", {
        method: "POST",
        body: JSON.stringify({
            search_query: string 
        }),
        headers: {
            "Content-type": "application/json; charset=UTF-8"
        }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error("Chyba při načítání dat z API.");
        }
        return response.json();
    })
    .then(function(responseData) {
  //      console.log(responseData.product_name); // Debugging, co se vrací z API
        if (inputId) {
            document.getElementById('inputBulk' + inputId).value = responseData.product_sku; // Vyplnění pouze aktivního inputu
            document.getElementById('nameSpanBulk' + inputId).textContent = responseData.product_name;
            // Zápis do odpovídajícího <span>
            document.getElementById('searchResultBulkAddToCart' + inputId).innerHTML = ''; // Prázdný obsah
        let inputBulkElement = document.getElementById('pocetBulk' + inputId);
        if (inputBulkElement) {
            inputBulkElement.focus(); // Nastavení fokusu zpět na aktivní input
        }
        }
    })
    .catch(function(error) {
        console.error("Došlo k chybě při zpracování požadavku:", error);
    });
}

function loadData(query) {
    if (query.length > 2) {
        let form_data = new FormData();
        form_data.append('query', query);
        let ajax_request = new XMLHttpRequest();
        ajax_request.open('POST', 'index.php?option=com_ajax&module=virtuemart_zbulkaddtocart&format=raw', true);
        ajax_request.send(form_data);
        ajax_request.onreadystatechange = function() {
            if (ajax_request.readyState === 4 && ajax_request.status === 200) {
                let responseOk = JSON.parse(ajax_request.responseText);
                
                let html = '<div class="list-group">';
                let activeInput = document.querySelector('input[type="text"]:focus');
                if (activeInput) {
                    // Získání čísla z ID inputu
                    let inputIdNumber = activeInput.id.replace('inputBulk', '');

                if (responseOk.length > 0) {
                    for (let count = 0; count < responseOk.length; count++) {
                        const newLocal = '<a href="#" class="list-group-item_b list-group-item_b-action"' + "list_input_id="+inputIdNumber +'>';
                        html += newLocal + responseOk[count].product_name + '</a>';
                    }
                } else {
                    html += '<a href="#" class="list-group-item_b list-group-item_b-action disabled">No Data Found</a>';
                }
                html += '</div>';
                
                    // Zápis do odpovídajícího <span>
                    document.getElementById('searchResultBulkAddToCart' + inputIdNumber).innerHTML = html;
                }
            }
        };
    } else {
        let activeInput = document.querySelector('input[type="text"]:focus');
        if (activeInput) {
            let inputIdNumber = activeInput.id.replace('inputBulk', '');
            document.getElementById('searchResultBulkAddToCart' + inputIdNumber).innerHTML = '';
    }
}
}

function isCheckboxChecked() {
    // Získání checkboxu podle ID
    const checkbox = document.getElementById('bulkHledatkoCheck');
    
    // Kontrola, zda je checkbox zaškrtnutý
    return checkbox.checked;
}

document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('bulkHledatkoCheck');
    
    // Přidání posluchače události 'change' pro checkbox
    checkbox.addEventListener('change', function() {
        if (isCheckboxChecked()) {

    // Získání všech inputů s ID
    let inputs = document.querySelectorAll('input[type="text"]');

    // Přidání posluchače události 'keydown' pro každý input
    inputs.forEach(input => {
        input.addEventListener('keydown', function(event) {
        });

         input.addEventListener('keyup', function() {
            // Získání aktuální hodnoty inputu
            let value = this.value;

            // Volání funkce loadData s aktuální hodnotou
            loadData(value);
        });
    });

    document.addEventListener('click', function(event) {
        if (event.target && event.target.matches('a.list-group-item_b.list-group-item_b-action')) {
            // Získání ID inputu, ze kterého vychází hodnota
            let listInputValue = event.target.getAttribute('list_input_id');
            if (listInputValue) {
                getText(event.target, listInputValue); // Předání ID inputu do funkce getText
            }
        }
    });
        }
    });
});