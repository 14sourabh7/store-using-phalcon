$(document).ready(function () {
  $(".controller").click(function () {
    var controller = $(this).val();
    var url = new URLSearchParams(window.location.search).get("bearer");
    $.ajax({
      url: `/access/addactions?bearer=${url}`,
      method: "POST",
      data: { controller: controller },
      dataType: "json",
    }).done(function (response) {
      console.log(response);
      display(response);
    });
  });
});

function display(arr) {
  var html = "";
  for (var i = 0; i < arr.length; i++) {
    html += `
        <option value="${arr[i].replace(".phtml", "")}" name='action'>${arr[
      i
    ].replace(".phtml", "")}</option>
      `;
  }
  $(".action").html(html);
}
