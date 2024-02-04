<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test CRUD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
</head>
<body>
  <div class="container show" id="mahasiswa">
    <h3>
      Mahasiswa
      <button
        onclick="tambah()"
        class="btn btn-xs btn-primary float-right">
        Tambah Data
      </button>
    </h3>
    <hr>
    <table class="table table-bordered data-table table-condensed table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Jenis Kelamin</th>
          <th>Alamat</th>
          <th>Total Mata Kuliah</th>
          <th>#</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
      <tfoot>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Jenis Kelamin</th>
          <th>Alamat</th>
          <th>Total Mata Kuliah</th>
          <th>#</th>
        </tr>
      </tfoot>
    </table>
  </div>
  <div class="container hide" id="form">
    <h3>
      <label id="label"></label>
      <button
        onclick="tutup()"
        class="btn btn-xs btn-danger float-right">
        Kembali
      </button>
    </h3>
    <div class="mb-3">
      <label class="form-label">Nama</label>
      <input type="text" name="nama" id="nama" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Jenis Kelamin</label>
      <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
        <option value="Pria">Pria</option>
        <option value="Wanita">Wanita</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Alamat</label>
      <textarea name="alamat" id="alamat" cols="15" rows="5" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <button
        onclick="addMatkul()"
        class="btn btn-xs btn-primary float-right">
        Tambah
      </button>
      <table
        class="table table-bordered table-condensed table-striped"
      >
        <tbody id="datas"></tbody>
      </table>
    </div>
    <div class="mb-3">
      <button
        onclick="submit()"
        class="btn btn-xs btn-primary float-right"
        type="submit"
      >
        Simpan
      </button>
    </div>
  </div>
</body>
<style>
  .hide {
    display: none;
  }
  .show {
    display: block;
  }
</style>
<script type="text/javascript">
$(document).ready(function () {
  var table = $('.data-table').DataTable({
    ajax: {url: "http://localhost:8000/api/mahasiswa", dataSrc: ''},
    type: 'GET',
    dataType: 'json',
    contentType: 'application/json; charset=utf-8',
    columns: [
      {data: 'id', name: 'id'},
      {data: 'nama', name: 'nama'},
      {data: 'jenis_kelamin', name: 'jenis_kelamin'},
      {data: 'alamat', name: 'alamat'},
      {data: 'count', name: 'count'},
      {
        data: null,
        render: function ( data, type, row ) {
          return `<button onclick="edit(${row.id})" class="btn btn-xs btn-secondary btn-edit">Ubah</button>
          <button onclick="del(${row.id})" class="btn btn-xs btn-danger btn-delete">Haous</button>`;
        }
      }
    ]
  })  
})

let form = document.getElementById("form")
let mahasiswa = document.getElementById("mahasiswa")
var nama = document.getElementById('nama')
var jenis_kelamin = document.getElementById('jenis_kelamin')
var alamat = document.getElementById('alamat')
var datas = document.getElementById('datas')
let label = document.getElementById("label")
let dataId = 0

let mata_kuliah = []

function tambah() {
  label.innerHTML = 'Tambah'
  form.classList.add("show")
  form.classList.remove("hide")
  mahasiswa.classList.add("hide")
  mahasiswa.classList.remove("show")

  mata_kuliah = [{
    nama: ""
  }]
  nama.value = ""
  jenis_kelamin.value = ""
  alamat.value = ""

  tambah_matakuliah()
}

function tutup() {
  form.classList.add("hide")
  form.classList.remove("show")
  mahasiswa.classList.add("show")
  mahasiswa.classList.remove("hide")
}

function edit(id) {
  label.innerHTML = 'Ubah'
  form.classList.add("show")
  form.classList.remove("hide")
  mahasiswa.classList.add("hide")
  mahasiswa.classList.remove("show")
  $.ajax({
    url: `http://localhost:8000/api/mahasiswa/${id}`,
    method:"GET",
    dataType:"JSON",
    contentType: 'application/json; charset=utf-8',
    success:function(dt)
    {
      let mahasiswa = dt.mahasiswa
      mata_kuliah = dt.mata_kuliah
      nama.value = mahasiswa.nama
      jenis_kelamin.value = mahasiswa.jenis_kelamin
      alamat.value = mahasiswa.alamat
      dataId = id

      tambah_matakuliah()
    }
  })
}

function tambah_matakuliah() {  
  datas.innerHTML = ``
  var tr = ``
  mata_kuliah.forEach((x, i)=>{
    tr += `<tr>
      <td>
        <input
          type="text"
          id="matakuliah${i}"
          onkeyup="matakuliah(${i})"
          onkeydown="matakuliah(${i})"
          value="${x.nama}"
          class="form-control">
      </td>
      <td>
        <button
          onclick="delMatkul(${i})"
          class="btn btn-xs btn-danger">
          Hapus
        </button>
      </td>
    </tr>`
  })
  datas.innerHTML = tr
}

function addMatkul() {
  mata_kuliah.push(
    {
      nama: ""
    }
  )
  tambah_matakuliah()
}

function delMatkul(i) {
  mata_kuliah.splice(i, 1)
  tambah_matakuliah()
}

function del(id) {
  $.ajax({
    url: `http://localhost:8000/api/mahasiswa/${id}`,
    method:"DELETE",
    success:function(dt)
    {
      alert('Succesfully deleted')
    }
  })
}

function matakuliah(i) {
  let matkul = document.getElementById(`matakuliah${i}`)
  if (matkul.value.length >= 4) {
    mata_kuliah[i] = {
      nama: matkul.value
    }
  }
}

function submit() {
  if (label.innerHTML === 'Tambah') {
    $.ajax({
      url: `http://localhost:8000/api/mahasiswa`,
      method:"POST",
      contentType: 'application/json; charset=utf-8',
      data: {
        nama: nama.value,
        jenis_kelamin: jenis_kelamin.value,
        alamat: alamat.value,
        mata_kuliah: mata_kuliah
      },
      dataType:"JSON",
      success: function(dt) {
        alert(`Successfully created data`)
      }
    })
  }
  else {
    $.ajax({
      url: `http://localhost:8000/api/mahasiswa/${dataId}`,
      method:"PUT",
      contentType: 'application/json; charset=utf-8',
      data: {
        nama: nama.value,
        jenis_kelamin: jenis_kelamin.value,
        alamat: alamat.value,
        mata_kuliah: mata_kuliah
      },
      dataType:"JSON",
      success: function(dt) {
        alert(`Successfully updated data`)
      }
    })
  }
}
</script>
</html>