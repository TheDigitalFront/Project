<?php

/* Template Name: Registration Page
 * Front-end user registration for The Digital Front.
 * @package TheDigitalFront
 * @since   1.0.0
 */

get_header(); /* loads the site header including nav and breaking news banner */
// Shows front-end registration form when not logged in
?>

<main class="tdf-page">
    <!-- <main> landmark improves accessibility and SEO by identifying the primary content area -->
    <div class="tdf-container tdf-container--narrow">
        <!-- Narrow container centres the form to a comfortable reading/input width -->

        <?php if (function_exists('yoast_breadcrumb')) : /* checks if Yoast SEO is active before trying to output breadcrumbs */ ?>
            <nav class="tdf-breadcrumbs" aria-label="Breadcrumb">
                <?php yoast_breadcrumb('<p>', '</p>'); /* outputs the breadcrumb trail */ ?>
                <!-- Breadcrumb trail helps users orient themselves within the site hierarchy -->
            </nav>
        <?php endif; ?>

        <header class="tdf-page__header" style="text-align: center;">
            <!-- Page header contains the primary H1 and a short subtitle -->
            <h1 class="tdf-page__title">Create an Account</h1>
            <p class="tdf-page__subtitle">Join The Digital Front community</p>
        </header>

        <div class="tdf-page__content">
            <!-- Content area switches between a logged-in notice and the registration form -->

            <?php if (is_user_logged_in()) : /* if the user is already logged in there is no need to show the form */ ?>
                <!-- Prevent already-authenticated users from seeing or submitting the form -->

                <p>
                    You are already logged in.
                    <a href="<?php echo esc_url(home_url('/')); /* sends the logged in user back to the home page */ ?>">Go to Home</a>
                    <!-- home_url('/') always resolves to the correct home URL regardless of subdirectory installs -->
                </p>

            <?php else : /* user is not logged in so show the registration form */ ?>

                <?php
                /* checks the URL for a registration=disabled param — WordPress adds this when registration is off in Settings */
                if (isset($_GET['registration']) && $_GET['registration'] === 'disabled') :
                    // WordPress appends ?registration=disabled to the redirect URL when registration is turned off in Settings > General. 
                ?>
                    <p class="tdf-form__error">User registration is currently disabled.</p>
                <?php endif; ?>

                <!-- Registration Form -->
                <!-- method="post" keeps credentials out of the browser history and server logs -->
                <form
                    class="tdf-form"
                    method="post"
                    action="<?php echo esc_url(site_url('wp-login.php?action=register', 'login_post')); /* submits to WP's registration handler */ ?>">
                    <!-- site_url() with 'login_post' scheme ensures the correct protocol (https) is used -->

                    <div class="tdf-form__group"> <!-- Username Field div -->
                        <label class="tdf-form__label" for="user_login">Username</label><!-- visible username used for login -->
                        <!-- for="user_login" associates the label with the input for accessibility -->
                        <input
                            class="tdf-form__input"
                            type="text"
                            name="user_login" <?php /* name must be "user_login" — WP's registration handler reads this key */ ?>
                            id="user_login"
                            autocomplete="username" <?php /* hints to the browser's autofill to suggest saved usernames */ ?>
                            required />
                    </div>

                    <div class="tdf-form__group"> <!-- Email Field div -->
                        <label class="tdf-form__label" for="user_email">Email Address</label> <!-- used to send generated password -->
                        <!-- type="email" triggers native browser validation and the correct mobile keyboard -->
                        <input
                            class="tdf-form__input"
                            type="email"
                            name="user_email" <?php /* name must be "user_email" — WP's registration handler reads this key */ ?>
                            id="user_email"
                            autocomplete="email" <?php /* hints to the browser's autofill to suggest saved email addresses */ ?>
                            required />
                    </div>

                    <!-- Informs the user that WordPress will auto-generate and email a password; no password field is needed on this form -->
                    <p class="tdf-form__note">A password will be sent to your email address.</p>

                    <?php
                    // Allow plugins to insert extra registration fields (keeps extensible)
                    do_action('register_form');
                    // Fires inside the registration form; third-party plugins (e.g. for GDPR
                    // consent checkboxes or extra profile fields) hook in here
                    ?>

                    <div class="tdf-form__group">
                        <button type="submit" class="tdf-btn tdf-btn--primary"> <!-- Submit button; type="submit" triggers native form submission -->
                            Register
                        </button>
                    </div>

                </form> <!--  end of the form  -->

                <!-- considering someone already has an account and wants to log in -->
                <!-- Offers an escape hatch for users who landed here by mistake -->
                <p class="tdf-form__alt-link">
                    Already have an account?
                    <a href="<?php echo esc_url(wp_login_url()); /* generates the correct login URL for this WordPress install */ ?>">Log in here</a>
                </p>

            <?php endif; ?>

        </div>
    </div>
</main>

<?php get_footer(); /* loads the site footer and fires wp_footer for scripts */ ?>