import "@babel/polyfill";
import MicroModal from "micromodal";
import personInfoTemplate from "./templates/personInfo";

const showPersonInfo = async event => {
  const listItem = event.currentTarget;

  // No content associated to the element
  if (listItem && !listItem.dataset.content) {
    return;
  }

  const personInfo = JSON.parse(listItem.dataset.content);
  const personnelListModalContent = document.querySelector(
    ".js-personnelList_item_modal_content"
  );

  // No modal rendered, no place to render the content
  if (!personnelListModalContent) {
    return;
  }

  personnelListModalContent.innerHTML = await personInfoTemplate(personInfo);
  MicroModal.show("personnelList_item_modal");
};

document.addEventListener("DOMContentLoaded", function() {
  const listItems = [...document.querySelectorAll(".js-personnelList_item")];

  if (!listItems) {
    return;
  }

  listItems.forEach(element => {
    element.addEventListener("click", showPersonInfo);
  });
});
