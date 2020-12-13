import Choices from "choices.js";

const USER_BUTTON_ACTIONS = {
  add: 'remove',
  remove: 'add'
};

let typingTimer;
let doneTypingInterval = 1000;
let searchTagCache = {};

function getCorrectAmountValue(input) {
  let amount = input.value;

  if (amount.includes(',')) {
    amount = amount.replace(',', '.');
  }

  if (!isNumeric(amount)) {
    return null;
  }

  const truncated = amount.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0]
  input.value = truncated;

  return truncated;
}

function isNumeric(str) {
  if (typeof str != "string") {
    return false
  }

  return !isNaN(str) && !isNaN(parseFloat(str));
}

function removeUserRow(button) {
  const { id, action } = button.dataset;

  // remove row
  const row = document.querySelector(`[data-component="collection-item"][data-number="${id}"]`);
  row.remove();

  // update button
  button.dataset.action = USER_BUTTON_ACTIONS[action];
  button.classList.remove('btn-secondary');
  button.classList.add('btn-outline-secondary');
}

function addUserRow(button) {
  const { id, name, action } = button.dataset;
  const { prototype } = document.querySelector('[data-component="collection-form"]').dataset;

  // update row with user id
  let row = prototype.replace(/__name__/g, id);

  // update button
  button.dataset.action = USER_BUTTON_ACTIONS[action];
  button.classList.remove('btn-outline-secondary');
  button.classList.add('btn-secondary');

  // add row to the container
  document.querySelector('[data-component="collection-container"]').insertAdjacentHTML('beforeend', row);

  // get node added
  row = document.querySelector(`[data-component="collection-item"][data-number="${id}"]`);

  // add user name to the row title
  row.querySelector('[data-component="collection-item-title"]').innerText = name;

  // add user id to the input
  row.querySelector(`input#expense_expenseUsers_${id}_user`).value = parseInt(id, 10);
}

function handleUserButton(event) {
  event.preventDefault();

  const button = event.currentTarget;
  const { action } = button.dataset;

  switch (action) {
    case 'add':
      addUserRow(button);
      break;
    case 'remove':
      removeUserRow(button);
      break;
  }

  updatePartPerPerson();
}

function handleCloseRow(element) {
  const { id } = element.dataset;
  const userButton = document.querySelector(`button[data-id="${id}"]`);
  if (userButton) {
    removeUserRow(userButton);
  }
}

function handleContainerClick(event) {
  const element = event.target;
  switch (element.dataset.component) {
    case 'close-row':
      handleCloseRow(element);
      break;
    default:
      break;
  }
}

function updatePartPerPerson(amount = null) {
  if (amount === null) {
    handleAmountInput()
  } else {
    const personItems = document.querySelectorAll('[data-component="collection-item"]');
    const peopleNumber = personItems.length;
    if (peopleNumber > 0) {
      const partPerPerson = Math.round((parseFloat(amount) / peopleNumber) * 100) / 100;
      personItems.forEach((personItem) => {
        personItem.querySelector('span[data-component="person-part"]').innerText = partPerPerson;
      });
    }
  }
}

function handleAmountInput() {
  const amountInput = document.querySelector('[data-component="total-amount"]');
  amountInput.classList.remove('is-invalid');

  const amount = getCorrectAmountValue(amountInput);

  if (amount === null) {
    amountInput.classList.add('is-invalid');
    updatePartPerPerson(0);
  } else {
    updatePartPerPerson(amount);
  }
}

function populateOptions(options, choices, query) {
  const items = choices._currentState.items;
  const toRemove = [];
  for (let i = 0; i < items.length; i++) {
    const item = items[i];
    if (item.active) {
      toRemove.push(item.value);
    }
  }

  const toKeep = [];
  if (options.length > 0) {
    for (let i = 0; i < options.length; i++) {
      const result = options[i];
      if (!toRemove.includes(result)) {
        toKeep.push({id: i, value: result, label: result});
      }
    }
  } else {
    toKeep.push({id: 1, value: query, label: query});
  }

  choices.setChoices(toKeep, 'value', 'label', true);
}

function searchTagReady(event, choices, query) {
  const httpRequest = event.currentTarget;
  if (httpRequest.readyState === 4) {
    if (httpRequest.status === 200) {
      const data = JSON.parse(httpRequest.response);
      searchTagCache[query] = data;
      populateOptions(data, choices, query);
    }
  }
}

function searchTag(url, choices) {
  const query = choices.input.value;
  if (query in searchTagCache) {
    populateOptions(searchTagCache[query], choices, query);
  } else {
    const httpRequest = new XMLHttpRequest();
    const body = {query: query};

    httpRequest.onreadystatechange = (event) => searchTagReady(event, choices, query);
    httpRequest.open('POST', url);
    httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    httpRequest.setRequestHeader('Content-Type', 'application/json');

    httpRequest.send(JSON.stringify(body));
  }
}

function initTagsSelect(element) {
  const { translations } = window;
  if (translations === undefined || translations === null) {
    throw Error('No Translations defined.');
  }

  const config = {
    placeholder: true,
    searchChoices: false,
    shouldSort: false,
    searchFloor: 2,
    placeholderValue: translations.selector.placeholder,
    loadingText: translations.selector.loading,
    noResultsText: translations.selector.no_results,
    noChoicesText: translations.selector.no_choices,
    itemSelectText: translations.selector.item_select,
    maxItemCount: 3,
    removeItemButton: true
  };

  const choices = new Choices(element, config);
  const { url } = element.dataset;

  element.addEventListener('search', () => {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(searchTag.bind(null, url, choices), doneTypingInterval);
  });

  element.addEventListener('choice', () => {
    choices.setChoices([], 'id', 'text', true);
  });
}

function initNewPage() {
  if (document.querySelector('[data-component="collection-form"]')) {
    const userButtons = document.querySelectorAll('[data-component="collection-item-selector"]');
    if (userButtons) {
      userButtons.forEach((userButton) => {
        userButton.addEventListener('click', handleUserButton);
      });
    }

    const collectionContainer = document.querySelector('[data-component="collection-container"]');
    if (collectionContainer) {
      collectionContainer.addEventListener('click', handleContainerClick);
    }
  }

  const amountInput = document.querySelector('[data-component="total-amount"]');
  if (amountInput) {
    amountInput.addEventListener('keyup', () => {
      clearTimeout(typingTimer);
      if (amountInput.value) {
        typingTimer = setTimeout(handleAmountInput, doneTypingInterval);
      }
    });
  }

  const tagsSelect = document.querySelector('[data-component="tags-select"]');
  if (tagsSelect) {
    initTagsSelect(tagsSelect);
  }
}

function init() {
  if (document.querySelector('[data-page="new"]')) {
    initNewPage();
  }
}

export default init;
