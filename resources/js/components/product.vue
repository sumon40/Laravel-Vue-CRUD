<template>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h2>Laravel and Vue JS CRUD Without Page Reload</h2>
                        <button type="button" class="btn btn-success" @click="newmodal">Add Product</button>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Discription</th>
                                    <th>Photo</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in allproducts" :key='product.id'>
                                    <td>{{ product.title }}</td>
                                    <td>{{ product.description }}</td>
                                    <td>
                                         <img width="100" height="100" :src="showphoto(product.photo)" alt="">   
                                    </td>
                                    <td style="font-size: 25px">
                                        <a href="#" @click="editmodal(product)">
                                            <i class="far fa-edit text-info"></i>
                                        </a>/
                                        <a href="#" @click="deleteproduct(product.id)">
                                            <i class="fas fa-trash text-danger"></i>
                                        </a>
                                    </td>
                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <!-- Modal -->
                    <div class="modal fade" id="productModal">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 v-show="!editmode" class="modal-title" id="exampleModalCenterTitle">Add Product</h5>
                                    <h5 v-show="editmode" class="modal-title" id="exampleModalCenterTitle">Update Product</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form @submit.prevent="editmode ? update() : create()">
                                    <div class="modal-body ">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input v-model="form.title" type="text" name="title"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                                            <has-error :form="form" field="title"></has-error>
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea  v-model="form.description" type="text" name="description"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('description') }"></textarea>
                                            <has-error :form="form" field="description"></has-error>
                                        </div>
                                        <div class="form-group">
                                            <label>Photo</label>
                                            <input type="file"  name="photo" @change="onphoto"
                                                class="form-control" :class="{ 'is-invalid': form.errors.has('photo') }">
                                            <has-error :form="form" field="photo"></has-error>

                                            <img width="100" v-show="editmode" :src="editmode ? editmodalphoto() : ''" alt="">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                                        <button v-show="editmode" type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- set progressbar -->
        <vue-progress-bar></vue-progress-bar>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                editmode: false,
                allproducts: {},
                form: new Form({
                    id: '',
                    title: '',
                    description: '',
                    photo: '',
                })
            }
        },

        methods: {
            newmodal () {
               this.editmode = false,
               this.form.reset();
               $('#productModal').modal('show'); 
            },
            editmodal (product) {
               this.editmode = true,
               this.form.reset();
               $('#productModal').modal('show');
               this.form.fill(product); 
            },
            editmodalphoto() {
                let photo = (this.form.photo.length > 200) ? this.form.photo : "uploads/product/" + this.form.photo;
                return photo;
            },
            showphoto(name) {
                return "uploads/product/" + name;
            },
            load() {
                axios.get('all/product')
                .then( ({data}) => (this.allproducts = data.data));
            },
            onphoto (e) {
                let type = ['image/jpe', 'image/jpeg', 'image/png', 'image/gif']
                let file = e.target.files[0];
                let reader = new FileReader();
                if (type.includes(file['type'])) {
                    if (file['size'] <= 2097152) {
                        reader.onloadend = (file) => {
                           this.form.photo = reader.result; 
                        }
                        reader.readAsDataURL(file);
                    } else {
                        this.form.photo = ''; 
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: 'The maximum size of image size is 2 MB',
                        })
                    }
                } else {
                    this.form.photo = ''; 
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'The image must be JPG JPEG PNG GIF ',
                    })  
                }

            },
            create() {
                this.$Progress.start();
                this.form.post('/product')
                .then(() => {
                    $('#productModal').modal('hide');
                    Toast.fire({
                        type: 'success',
                        title: 'Product Created Successfully'
                    });
                    without.$emit('liveload');
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },
            update () {
                this.$Progress.start();
                this.form.put('/product/' + this.form.id)
                .then( () => {
                    $('#productModal').modal('hide');
                    Toast.fire({
                        type: 'success',
                        title: 'Product Updated Successfully'
                    });
                    without.$emit('liveload');
                    this.$Progress.finish();
                })
                .catch( () => {
                    this.$Progress.fail();
                });
            },
            deleteproduct(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2d995b',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.$Progress.start();
                        this.form.delete('/product/' +id )
                        .then( () => {
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            )
                            without.$emit('liveload');
                            this.$Progress.finish();
                        })
                        .catch( () => {
                            this.$Progress.fail();
                        });
                        
                    }
                })
            },
        },

        mounted() {
            this.load();
            without.$on('liveload', () => {
                this.load();
            });
        }
    }
</script>
