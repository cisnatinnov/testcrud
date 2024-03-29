@extends('layouts.default')
@section('content')
  <div class="col-sm-12 show" id="mahasiswa">
    <h3>
      Mahasiswa
      <button
        onclick="tambah()"
        class="btn btn-xs btn-primary float-right">
        Tambah Data
      </button>
    </h3>
    <hr>
    <table class="table table-striped table-bordered data-table" style="width:100%">
      <thead>
        <tr>
          <th class="text-center hide">ID</th>
          <th class="text-center">#</th>
          <th class="text-center">Nama</th>
          <th class="text-center">Jenis Kelamin</th>
          <th class="text-center">Alamat</th>
          <th class="text-center">Total Mata Kuliah</th>
          <th class="text-center">#</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
      <tfoot>
        <tr>
          <th class="text-center hide">ID</th>
          <th class="text-center">#</th>
          <th class="text-center">Nama</th>
          <th class="text-center">Jenis Kelamin</th>
          <th class="text-center">Alamat</th>
          <th class="text-center">Total Mata Kuliah</th>
          <th class="text-center">#</th>
        </tr>
      </tfoot>
    </table>
  </div>
  <div class="col-sm-12 hide" id="form">
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
      <label class="form-label">SKS</label>
      <input type="file" name="file" id="file" accept=".pdf, .docx, doc, .xlsx, .xls">
    </div>
    <div class="mb-3">
      <a id="berkas" class="hide" href="" download>SKS</a>
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
  <style>
    .hide {
      display: none;
    }
    .show {
      display: block;
    }
  </style>
  <script type="text/javascript">
  var table = $('.data-table').DataTable({
    processing: true,
    ajax: {url: "http://localhost:8000/api/mahasiswa", dataSrc: ''},
    type: 'GET',
    dataType: 'json',
    contentType: 'application/json; charset=utf-8',
    columns: [
      {data: 'id', name: 'id'},
      {data: null},
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
    ],
    columnDefs: [
      { targets: [ 0 ], visible: false },
      { targets: [ 0, 1, 3, 5, 6 ], className: 'dt-center' },
      { targets: [ 3, 6 ], orderable: false },
      { targets: [ 1, 6 ], searchable: false },
      { width: "5%", targets: [ 1 ] },
      { width: "10%", targets: [ 3, 5 ] },
      { width: "30%", targets: [ 2, 4 ] }
    ]
  })
  $(document).ready(function () {
    table.on('order.dt search.dt', function () {
      table.column(1, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
    }).draw()
  })

  let form = document.getElementById("form")
  let mahasiswa = document.getElementById("mahasiswa")
  var nama = document.getElementById('nama')
  var jenis_kelamin = document.getElementById('jenis_kelamin')
  var alamat = document.getElementById('alamat')
  var datas = document.getElementById('datas')
  let label = document.getElementById("label")
  let file = document.getElementById("file")
  let berkas = document.getElementById("berkas")
  var sks = ''
  let dataId = 0

  let mata_kuliah = []

  file.onchange = function() {
    let input = this.files[0];
    const reader = new FileReader();
    reader.readAsDataURL(input);
    reader.onload = () => {
      const fileBase64 = reader.result;
      sks = fileBase64;
      berkas.href = sks
      berkas.classList.add("show")
      berkas.classList.remove("hide")
    }
  }

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
    sks = ""

    tambah_matakuliah()
  }

  function tutup() {
    form.classList.add("hide")
    form.classList.remove("show")
    mahasiswa.classList.add("show")
    mahasiswa.classList.remove("hide")

    setInterval('location.reload()', 2000);
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
        sks = mahasiswa.sks
        dataId = id
        berkas.href = sks
        berkas.classList.add("show")
        berkas.classList.remove("hide")

        tambah_matakuliah()
      }
    })
  }

  function tambah_matakuliah() {  
    datas.innerHTML = ``
    var tr = ``
    mata_kuliah.forEach((x, i)=>{
      tr += `<tr>
        <td>${i+1}</td>
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
        setInterval('location.reload()', 2000);
      }
    })
  }

  function matakuliah(i) {
    let matkul = document.getElementById(`matakuliah${i}`)
    mata_kuliah[i] = {
      nama: matkul.value
    }
  }

  function submit() {
    if (label.innerHTML === 'Tambah') {
      $.ajax({
        url: `http://localhost:8000/api/mahasiswa`,
        method:"POST",
        contentType: 'application/json; charset=utf-8',
        data: JSON.stringify({
          nama: nama.value,
          jenis_kelamin: jenis_kelamin.value,
          alamat: alamat.value,
          sks: sks,
          mata_kuliah: mata_kuliah
        }),
        dataType:"JSON",
        success: function(dt) {
          alert(`Successfully created data`)
          tutup()
        }
      })
    }
    else {
      $.ajax({
        url: `http://localhost:8000/api/mahasiswa/${dataId}`,
        method:"PUT",
        contentType: 'application/json; charset=utf-8',
        data: JSON.stringify({
          nama: nama.value,
          jenis_kelamin: jenis_kelamin.value,
          alamat: alamat.value,
          sks: sks,
          mata_kuliah: mata_kuliah
        }),
        dataType:"JSON",
        success: function(dt) {
          alert(`Successfully updated data`)
          tutup()
        }
      })
    }
  }
  </script>

@stop