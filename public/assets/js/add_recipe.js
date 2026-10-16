const divRecipeSteps = document.getElementById("recipeSteps");
const divRecipeIngredients = document.getElementById("recipeIngredients");
let amountOfSteps = 1;
let amountOfIngredients = 1;
const fakeInputForTags = document.getElementById("fakeInputForTags");
const realInputForTags = document.getElementById("inputForRecipeTags");
const divForTags = document.getElementById("divForRecipeTags");
let tags = new Map();
const assetImages = document.querySelector("header img")?.getAttribute("src")?.replace(/pot\.png$/, "") || "assets/images/";

function apply_auto_height_to_textareas() {
        document.querySelectorAll("textarea").forEach(function(textarea) {
                textarea.style.height = textarea.scrollHeight + "px";
                textarea.style.overflowY = "hidden";
                if (textarea.dataset.autoHeight === "1") {
                        return;
                }
                textarea.dataset.autoHeight = "1";
                textarea.addEventListener("input", function() {
                        this.style.height = "auto";
                        this.style.height = this.scrollHeight + "px";
                });
        });
}

function unit_options_html() {
        const first = divRecipeIngredients.querySelector("select");
        if (!first) {
                return '<option value="1" selected>g</option>';
        }

        return Array.from(first.options).map(function(option) {
                return `<option value="${option.value}"${option.value === "1" ? " selected" : ""}>${option.text}</option>`;
        }).join("");
}

function add_recipe_ingredient() {
        amountOfIngredients++;
        divRecipeIngredients.insertAdjacentHTML("beforeend", `<div> <input type="text" maxlength="255" placeholder="Nom de l'ingrédient" name="RecipeIngrName${amountOfIngredients}" class="border w-5/8"> <input type="number" min="0" max="10000" step="any" placeholder="Quantité" name="RecipeIngrVal${amountOfIngredients}" class="border w-1/8"> <select name="RecipeIngrUnit${amountOfIngredients}" class="border w-1/8">${unit_options_html()}</select> </div>`);
}

function add_recipe_text() {
        amountOfSteps++;
        divRecipeSteps.insertAdjacentHTML("beforeend", `<div class="flex w-full items-center"> <p class="text-l w-20">Étape ${amountOfSteps}</p> <textarea class="border rounded-xl w-full h-auto text-clip p-2" name="RecipeText${amountOfSteps}"></textarea> </div>`);
        apply_auto_height_to_textareas();
}

function update_hidden_tag_input_value() {
        realInputForTags.value = Array.from(tags.keys()).join(";");
}

function remove_tag(tag) {
        tags.delete(tag);
        update_hidden_tag_input_value();
        document.getElementById(`tag${tag}`).remove();
}

fakeInputForTags.addEventListener("keydown", (event) => {
        if (event.key == " ") {
                let tag = fakeInputForTags.value.replace(/\s/g, "");
                if (tag !== "" && !tags.has(tag)) {
                        tags.set(tag, true);
                        divForTags.insertAdjacentHTML("beforeend", `<div id="tag${tag}" class="border rounded-xl px-3 flex items-center gap-1"><p>${tag}</p><button type="button" onclick="remove_tag('${tag}')"><img src="${assetImages}cross.png" alt="cross" class="w-4 h-4 cursor-pointer"></button></div>`);
                        update_hidden_tag_input_value();
                }
                fakeInputForTags.value = "";
        }
});

amountOfIngredients = Math.max(1, divRecipeIngredients.children.length);
amountOfSteps = Math.max(1, divRecipeSteps.children.length);
(realInputForTags.value || "").split(";").forEach(function(tag) {
        if (tag !== "") {
                tags.set(tag, true);
        }
});
apply_auto_height_to_textareas();
