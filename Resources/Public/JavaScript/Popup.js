window.onload = () => {

  let queryString = window.location.search;
  let urlParams = new URLSearchParams(queryString);

  if (urlParams.get('nsSocialLogin') && urlParams.get('logintype')) {
    let originalUrl = window.location.href;

    let paramsToRemove = ['logintype', 'nsSocialLogin'];

    let newUrl = removeParameters(originalUrl, paramsToRemove);

    window.location.href = newUrl;
  }
}

function removeParameters(url, paramsToRemove) {
  
  const urlObj = new URL(url);
  
  paramsToRemove.forEach(param => urlObj.searchParams.delete(param));
  
  return urlObj.toString();
}
