<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OLMS</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="/olms/asset/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
      integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
  </head>

  <body>
    
    <?php
  include __DIR__ . '/navbar.php';
    ?>
    
    
     <div class="container my-4">
      <div class="row g-4">
        <div class="col-12 col-sm-6 col-md-6 col-lg-3 aboutus-card">
          <div class="card text-center">
            <img
              src="/olms/asset/images/male-avtar.jpg"
              class="rounded-circle mx-auto"
              alt="..."
            />
            <h5>Tanish Sharma</h5>
            <p class="text-muted"></p>
            <p class="small"></p>
            <div>
              <a href="https://github.com/tanish418"
                ><button id="github-link-button">
                  <i class="fab fa-github"></i>GitHub
                  </button
              ></a>
              <a href="https://www.linkedin.com/in/tanish-sharma-ts4181/"
                ><button id="linkedin-link-button">
                  <i class="fa-brands fa-linkedin-in"></i>Linkedin
                </button></a
              >
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-md-6 col-lg-3 aboutus-card">
          <div class="card text-center">
            <img
              src="/olms/asset/images/male-avtar.jpg"
              class="rounded-circle mx-auto"
              alt="..."
            />
            <h5>Mehul Suthar</h5>
            <p class="text-muted"></p>
            <p class="small"></p>
            <div>
              <a href="https://github.com/Mehulsuthar817"
                ><button id="github-link-button">
                  <i class="fab fa-github"></i>GitHub
                  </button
              ></a>
              <a href="https://www.linkedin.com/in/mehul-suthar-aabb82298"
                ><button id="linkedin-link-button">
                  <i class="fa-brands fa-linkedin-in"></i>Linkedin
                </button></a
              >
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-md-6 col-lg-3 aboutus-card">
          <div class="card text-center">
            <img
              src="/olms/asset/images/male-avtar.jpg"
              class="rounded-circle mx-auto"
              alt="..."
            />
            <h5>Sajal Singhal</h5>
            <p class="text-muted"></p>
            <p class="small"></p>
            <div>
              <a href="https://github.com/sajalsinghal2005"
                ><button id="github-link-button">
                  <i class="fab fa-github"></i>GitHub
                  </button
              ></a>
              <a href="https://www.linkedin.com/in/sajal-singhal-446002318?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app"
                ><button id="linkedin-link-button">
                  <i class="fa-brands fa-linkedin-in"></i>Linkedin
                </button></a
              >
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-md-6 col-lg-3 aboutus-card">
          <div class="card text-center">
            <img
              src="/olms/asset/images/female avatar.webp"
              class="rounded-circle mx-auto"
              alt="..."
              width="224px"
              height="224px"
            />
            <h5>Shreya Saxena</h5>
            <p class="text-muted"></p>
            <p class="small"></p>
            <div>
              <a href="#"
                ><button id="github-link-button">
                  <i class="fab fa-github"></i>GitHub
                  </button
              ></a>
              <a href="#"
                ><button id="linkedin-link-button">
                  <i class="fa-brands fa-linkedin-in"></i>Linkedin
                </button></a
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php
  include __DIR__ . '/home_footer.php';
  ?>
    
  </body>
</html>
