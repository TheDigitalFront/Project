<?php

/**
 * Template Name: Registration Page
 *
 * Front-end user registration for The Digital Front.
 * @package TheDigitalFront
 * @since   1.0.0
 */

get_header(); /* loads the site header including nav and breaking news banner */
?>

<main class="tdf-page">
    <div class="tdf-container tdf-container--narrow">

        <?php if (function_exists('yoast_breadcrumb')) : /* checks if Yoast SEO is active before trying to output breadcrumbs */ ?>
            <nav class="tdf-breadcrumbs" aria-label="Breadcrumb">
                <?php yoast_breadcrumb('<p>', '</p>'); /* outputs the breadcrumb trail e.g. Home > Register */ ?>
            </nav>
        <?php endif; ?>

        <header class="tdf-page__header">
            <h1 class="tdf-page__title">Create an Account</h1>
        </header>

        <div class="tdf-page__content">

            <?php if (is_user_logged_in()) : /* if the user is already logged in there is no need to show the form */ ?>

                <p>
                    You are already logged in.
                    <a href="<?php echo esc_url(home_url('/')); /* sends the logged in user back to the home page */ ?>">Go to Home</a>
                </p>

            <?php else : /* user is not logged in so show the registration form */ ?>

                <?php
                /* checks the URL for a registration=disabled param — WordPress adds this when registration is turned off in Settings */
                if (isset($_GET['registration']) && $_GET['registration'] === 'disabled') :
                ?>
                    <p class="tdf-form__error">User registration is currently disabled.</p>
                <?php endif; ?>
                <!-- Registration Form accepts the following fields: user_login, user_email -->
                <form
                    class="tdf-form"
                    method="post"
                    action="<?php echo esc_url(site_url('wp-login.php?action=register', 'login_post')); /* submits to WordPress's built-in registration handler */ ?>">

                    <div class="tdf-form__group"> <!-- Username Field div -->
                        <label class="tdf-form__label" for="user_login">Username</label><!-- what the user sees -->
                        <input
                            class="tdf-form__input"
                            type="text"
                            name="user_login"
                            id="user_login"
                            autocomplete="username"
                            required />
                    </div>

                    <div class="tdf-form__group"> <!-- Email Field div -->
                        <label class="tdf-form__label" for="user_email">Email Address</label> <!--  what the user sees -->
                        <input
                            class="tdf-form__input"
                            type="email"
                            name="user_email"
                            id="user_email"
                            autocomplete="email"
                            required />
                    </div>

                    <p class="tdf-form__note">A password will be sent to your email address.</p> /* WordPress auto-generates and emails a password on successful registration */

                    <?php
                    do_action('register_form'); /* allows plugins like WPForms Lite to inject additional fields into the form if needed */
                    ?>
                    <div class="tdf-form__group">
                        <button type="submit" class="tdf-btn tdf-btn--primary">
                            Register
                        </button>
                    </div>
                </form> <!--  end of the form  -->
                <!-- considering someone already has an account and wants to log in -->
                <p class="tdf-form__alt-link">
                    Already have an account?
                    <a href="<?php echo esc_url(wp_login_url()); /* generates the correct login URL for this WordPress install */ ?>">Log in here</a>
                </p>

            <?php endif; ?>

        </div>
    </div>
</main>

<?php get_footer(); /* loads the site footer and fires wp_footer for scripts */ ?>