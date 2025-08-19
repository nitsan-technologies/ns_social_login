window.onload = () =>{
  let popupBtns = document.querySelectorAll('a[name="popupBtn"]');
  if(popupBtns.length) {
    popupBtns.forEach((item) => {
      let url = item.getAttribute('data-url');
      item.addEventListener('click', ()=> {
        auth_popup(url);
      })
    })
  }

  let queryString = window.location.search;
  let urlParams = new URLSearchParams(queryString);

  if (urlParams.get('nsSocialLogin') && urlParams.get('logintype')) {
    let originalUrl = window.location.href;

    let paramsToRemove = ['logintype', 'nsSocialLogin'];

    let newUrl = removeParameters(originalUrl, paramsToRemove);

    window.location.href = newUrl;
  }
}

function auth_popup(url) {
  var authWindow = window.open(url.trim(), 'authWindow', 'width=600,height=400,scrollbars=yes');
  window.closeAuthWindow = function () {
    authWindow.close();
  }

  return false;
}

function reloadParent() {
  location.reload(); // Reload the parent window
}

window.addEventListener("beforeunload", function(event) {
  window.opener.reloadParent(); // Call the reloadParent function of the parent window
});

function removeParameters(url, paramsToRemove) {
  
  const urlObj = new URL(url);
  
  paramsToRemove.forEach(param => urlObj.searchParams.delete(param));
  
  return urlObj.toString();
}
