

<!-- Modal -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <img id="modalImage" src="" alt="" style="width:100%; height:auto; margin-bottom: 20px;">
        <h2 id="modalTitle" style="text-align: center; margin-bottom: 20px;"></h2>
        
        <div style="text-align: center; margin-bottom: 20px;">
            <p id="modalRealPrices"></p>
            <p id="modalMayoreoPrices"></p>
            <p id="modalMeasures"></p>
            <p class="hideElement" id="modalId"></p>
        </div>
        
        <!-- Combo para seleccionar la cantidad de productos -->
        <div style="text-align: center; margin-bottom: 20px;">
            <h3>Cantidad:</h3>
            <select id="quantitySelect">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>

        <!-- Contenedor para colores y aromas en la misma fila -->
        <div style="display: flex; justify-content: space-around; margin-bottom: 20px;">
            <!-- Combo para seleccionar el color -->
            <div>
                <h3>Colores Disponibles:</h3>
                <select id="colorSelect">
                    <option value="Blanco">Blanco</option>
                    <option value="Negro">Negro</option>
                    <option value="Rojo">Rojo</option>
                    <option value="Azul">Azul</option>
                    <option value="Verde">Verde</option>
                </select>
            </div>

            <!-- Combo para seleccionar el aroma -->
            <div>
                <h3>Aromas Disponibles:</h3>
                <select id="aromaSelect">
                    <option value="Lavanda">Lavanda</option>
                    <option value="Vainilla">Vainilla</option>
                    <option value="Canela">Canela</option>
                    <option value="Jazmín">Jazmín</option>
                    <option value="Rosa">Rosa</option>
                </select>
            </div>
        </div>

        <!-- Botón para enviar la información -->
        <div style="text-align: center; margin-top: 20px;">
            <button id="addToCartButton" style="padding: 10px 20px; font-size: 16px;" onclick="addToCartButton()">Añadir al Carrito</button>
        </div>
    </div>
</div>


<script>
    // Get the modal
    var modal = document.getElementById("productModal");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // Function to open the modal and populate it with data
    function openModal(event) {
        var product = event.currentTarget.closest('.product');
        var imageSrc = product.querySelector('img').src;
        var title = product.querySelector('h2').innerText.replace('ver más', '').trim();  // Reemplaza 'ver más' y elimina espacios        var price = product.querySelector('p:nth-of-type(1)').innerText;
        var measures = product.querySelector('p:nth-of-type(2)').innerText;
        var realPrices = product.querySelector('p:nth-of-type(3)').innerText;
        var mayoreoPrices = product.querySelector('p:nth-of-type(4)').innerText;
        var id = product.querySelector('p:nth-of-type(5)').innerText;

        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalMeasures').innerText = measures;
        document.getElementById('modalRealPrices').innerText = realPrices;
        document.getElementById('modalMayoreoPrices').innerText = mayoreoPrices;
        document.getElementById('modalId').innerText = id;

        modal.style.display = "block";
    }

    // Add event listeners to each product image and title
    function addEventListenersToProducts() {
        var products = document.querySelectorAll('.product img, .product h2 .view-more');
        products.forEach(function(item) {
            item.addEventListener('click', openModal);
        });
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function addToCartButton() {

        var product_id = document.getElementById('modalId').innerText;
        var cantidad = document.getElementById('quantitySelect').value;
        var color = document.getElementById('colorSelect').value;
        var aroma = document.getElementById('aromaSelect').value;
        var username = getCookie("username");
        var status = "in_progress";

        $.ajax({
            url: 'https://api.velaaroma.com/v1/cart/products',
            type: "POST",
            data:'username=' + username + '&status=' + status + '&color=' + color + '&aroma=' + aroma
            + '&cantidad=' + cantidad + '&product_id=' + product_id,
            success: function(response){ 
                updateCartCount();
            }
        });

    }

    validate_login();
    updateCartCount();

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    } 

    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function deleteCookie(name) {
        document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }

    function validate_login(){

        console.log("Revisando inicio de sesión...");

        var username = getCookie("username");
        var name = getCookie("name");
        
        console.log("Username: " + username);
        console.log("Name: " + name);

        if(username == null){
            username = "usuario" + Math.floor(Math.random() * 1000) + "_" + Math.floor(Math.random() * 1000);
            setCookie("username", username ,30);

            //document.getElementById("close_sesion").classList.add("invisible");
            //document.getElementById("register").classList.add("hidden");

        } else {
            if(name == null){
            } else {
                var loginLink = document.querySelector('.login-link');
                loginLink.textContent = "Hola, " + name + "!";

                //document.getElementById("close_sesion").classList.add("hidden");
                //document.getElementById("register").classList.add("invisible");
                //document.getElementById("enter").classList.add("invisible");
            }
        }
    }

    function close_sesion(){
        deleteCookie("name");
        deleteCookie("username");
    }

    function updateCartCount() {

        var username = getCookie("username");
        var status = "in_progress";

        $.ajax({
            url: 'https://api.velaaroma.com/v1/cart/products',
            type: "GET",
            data:'username=' + username + '&status=' + status,
            success: function(response){ 
                document.querySelector('.cart-count').textContent = response.products;
            }
        });

    }

    function generateProductHTML(product) {
        return `
            <div class="product">
                <img src="${product.url}" alt="${product.name}">
                <h2>${product.name}<span class="view-more">ver más</span></h2>
                <p>Desde $${product.menudeo}.00 pesos</p>
                <p>Medidas: ${product.alto}x${product.ancho}x${product.largo} [cm]</p>
                <p class="hideElement">Precio: $${product.menudeo}</p>
                <p class="hideElement">Precio Mayoreo: $${product.mayoreo}</p>
                <p class="hideElement">${product.id}</p>
            </div>
        `;
    }

    function populateProducts(products) {
        const productContainer = document.getElementById('productContainer');
        let productHTML = '';

        products.forEach((product, index) => {
            productHTML += generateProductHTML(product);
            if ((index + 1) % 3 === 0) {
                productHTML += '<div style="flex-basis: 100%; height: 0;"></div>'; // Añade un separador cada 3 productos
            }
        });

        productContainer.innerHTML = productHTML;

        // Añadir event listeners a los nuevos elementos
        addEventListenersToProducts();
    }

    function getProductsByCategory(category) {
        $.ajax({
            url: 'https://api.velaaroma.com/v1/cart/products/category',
            type: "GET",
            data: 'category=' + category,
            success: function(response){ 
                console.log(response.products);
                populateProducts(response.products);
            }
        });
    }


</script>

<style>

    .hidden {
        display: none;
    }
    .invisible {
        visibility: hidden;
    }

    .product {
        width: 100%;
        max-width: 300px;
        margin: 10px;
        box-sizing: border-box;
    }

    .product img {
        width: 100%;
        height: auto;
        max-height: 250px;
        object-fit: cover;
        border-radius: 8px;
    }

</style>