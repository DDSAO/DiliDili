<div class="w-100 h-100 content" id="uploadPage">

   <form class="container" action="<?base_url()?>dashboard/upload">
        <div class="form-row my-2">
            <div class="form-group col-12 col-lg-6 mx-auto ml-lg-4">
                <label for="title">Video Title</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="">
            </div>
        </div>
        <div class="form-row my-2">
            <div class="form-group col-12 col-lg-6 mx-auto ml-lg-4">
                <label for="vdescription">Description</label>
                <textarea class="form-control" aria-label="description" name="description" id="vdescription"></textarea>
            </div>
        </div>
        <div class="form-row my-2">
            <div class="form-group col-12 col-lg-6 mx-auto ml-lg-4">
                <label for="tags">Tags</label>
                <input type="text" class="form-control" id="tags" name="tags" placeholder="Seperate by ,">
            </div>
        </div>
        <div class="form-row my-2">
            <div class="form-group col-12 col-lg-6 mx-auto ml-lg-4">
                <label for="category">Category</label>
                <select class="w-100 form-control" id="category">
                    <option value="funny">Funny</option>
                    <option value="animal">Animal</option>
                    <option value="music">Music</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>
        <div class="form-row my-2">
            <div class="form-group col-12 col-lg-6 mx-auto ml-lg-4">
                <label for="video">Upload Video (MP4 Format Only)</label>
                <input type="file" class="inputfile" id="video" name="video" accept=".mp4">
                <label for="video" class="form-control btn btn-primary" id="videoLabel">Select from device</label>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" id="progress"></div>
                </div>
            </div>
        </div>
        <div class="form-group col-12 col-lg-6 mx-auto ml-lg-4 d-flex justify-content-center">
            <img id="cover" />
        </div>
        
        
        <div class="form-row my-2">
            <div class="form-group col-12 col-lg-6 mx-auto ml-lg-4">
                <button class="btn btn-success w-100" type="submit" id="vsubmit" disabled>Confirm (You need to Upload First)</button>
            </div>
        </div>
        <div class="form-row my-2">
            <div class="form-group col-12 col-lg-6 mx-auto ml-lg-4">
                <div class="btn btn-success w-100 hide" id="resetUpload" disabled>Upload another video</div>
            </div>
        </div>
   </form>

</div>
<script type='text/javascript'>
    var videoNumber = null;
    var coverLocation = null;
    var videoLocation = null;
    $('#video').change(function(e){
        var info = e.target.files[0]
        if (info !== "undefined"){
            $("#videoLabel").text(info.name+" (size: "+formatBytes(info.size)+")")
        } 
        var data = new FormData()
        data.append('id',<?echo $id;?>)
        data.append('video',$("#video").prop('files')[0])
        var req = new XMLHttpRequest()
        req.open("POST", '<?echo base_url()?>dashboard/uploadVideo', true);
        
        req.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                let percentComplete = Math.ceil(e.loaded * 100 / e.total) ;
                $("#progress").css('width', percentComplete+'%').attr('aria-valuenow', percentComplete).text(percentComplete+'%')
            }
        };
        req.onreadystatechange = function() { // Call a function when the state changes.
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                $("#progress").removeClass("progress-bar-striped").addClass("bg-success").text("Upload Completed")
                console.log(req.response)
                let response = JSON.parse(req.response)
                videoNumber = response.videoNumber
                coverLocation = response.coverLocation
                videoLocation = response.videoLocation
                $("#cover").attr("src",'<?echo base_url()?>'+response.coverLocation)
                $("#vsubmit").prop("disabled",false).text("submit")
            }  
        }
        req.send(data)
    })
    $('#vsubmit').click(function(e){
        e.preventDefault()
        let data = new FormData()
        data.append('id', videoNumber)
        data.append('title',$("#title").val() !== ""? $("#title").val(): 'Name Not Specified')
        data.append('tags',$("#tags").val() !== ""? $("#tags").val(): 'NoTags')
        data.append('description',$("#vdescription").val() !== ""? $("#vdescription").val(): 'This lazy uploader did not leave any descriptions')
        data.append('category',$("#category").val())
        data.append('uploader',<?echo $id;?>)
        data.append('videoLocation',videoLocation)
        data.append('coverLocation',coverLocation)
        for (var pair of data.entries()) {
            console.log(pair[0]+ ', ' + pair[1]); 
        }

        fetch('<? echo base_url()?>dashboard/submitVideo',{
            method:'POST',
            body: data,
        }).then((response)=>{
            return response.json()
        }).then(response=>{
            console.log(response)

            if (response.success===1){
                $("#vsubmit").prop("disabled", true).text("Uploaded successfully")
                $("#resetUpload").removeClass('hide')
            } else {
                console.log('video not saved')
            }
        })
    })
    $("#resetUpload").click(function(){
        videoNumber = null;
        coverLocation = null;
        videoLocation = null;
        $("#uploadPage input").val('')
        $("#videoLabel").text("Select from device")
        $("#progress").css('width', 0+'%').attr('aria-valuenow', 0)
            .removeClass("bg-success").addClass("progress-bar-striped").text("")
        $("#cover").attr("src","")
        $("#vsubmit").text("Confirm (You need to Upload First)")
        $(this).addClass('hide')

    })
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        if (bytes > 1024*1024*1024) return 'file too large! (larger than 1GB)'
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script>
