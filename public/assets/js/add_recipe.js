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
        divRecipeIngredients.insertAdjacentHTML("beforeend", `<div class="ingredient-row"> <input type="text" maxlength="255" placeholder="Nom de l'ingrédient" name="RecipeIngrName${amountOfIngredients}" class="border"> <input type="number" min="0" max="10000" step="any" placeholder="Quantité" name="RecipeIngrVal${amountOfIngredients}" class="border"> <select name="RecipeIngrUnit${amountOfIngredients}" class="border">${unit_options_html()}</select> </div>`);
}

function add_recipe_text() {
        amountOfSteps++;
        divRecipeSteps.insertAdjacentHTML("beforeend", `<div class="recipe-step-row"> <p class="text-l w-full sm:w-20 shrink-0">Étape ${amountOfSteps}</p> <textarea class="border rounded-xl w-full h-auto text-clip p-2" name="RecipeText${amountOfSteps}"></textarea> </div>`);
        apply_auto_height_to_textareas();
}

function update_hidden_tag_input_value() {
        realInputForTags.value = Array.from(tags.keys()).join(";");
}

function add_tag_from_input() {
        const tag = (fakeInputForTags.value || "").trim().replace(/\s+/g, "");
        fakeInputForTags.value = "";
        if (tag === "" || tags.has(tag)) {
                update_hidden_tag_input_value();
                return;
        }

        tags.set(tag, true);
        const safe = tag.replace(/[&<>"']/g, "");
        divForTags.insertAdjacentHTML("beforeend", `<div id="tag${safe}" class="border rounded-xl px-3 flex items-center gap-1"><p></p><button type="button"><img src="${assetImages}cross.png" alt="cross" class="w-4 h-4 cursor-pointer"></button></div>`);
        const chip = document.getElementById(`tag${safe}`);
        if (chip) {
                chip.querySelector("p").textContent = tag;
                chip.querySelector("button").addEventListener("click", function() {
                        remove_tag(tag);
                });
        }
        update_hidden_tag_input_value();
}

function remove_tag(tag) {
        tags.delete(tag);
        update_hidden_tag_input_value();
        const chip = document.getElementById(`tag${tag.replace(/[&<>"']/g, "")}`);
        if (chip) {
                chip.remove();
        }
}

fakeInputForTags.addEventListener("keydown", (event) => {
        if (event.key === " " || event.key === "Enter" || event.key === ",") {
                event.preventDefault();
                add_tag_from_input();
        }
});

fakeInputForTags.addEventListener("blur", add_tag_from_input);

const recipeForm = document.getElementById("recipeForm");
recipeForm.addEventListener("keydown", (event) => {
        if (event.key !== "Enter") {
                return;
        }
        if (event.target === fakeInputForTags) {
                return;
        }
        if (event.target && event.target.tagName === "TEXTAREA") {
                return;
        }
        if (event.target && event.target.type === "submit") {
                return;
        }
        event.preventDefault();
});
recipeForm.addEventListener("submit", add_tag_from_input);

amountOfIngredients = Math.max(1, divRecipeIngredients.children.length);
amountOfSteps = Math.max(1, divRecipeSteps.children.length);
(realInputForTags.value || "").split(";").forEach(function(tag) {
        if (tag !== "") {
                tags.set(tag, true);
        }
});
apply_auto_height_to_textareas();
