<?php

/*
Plugin Name: Lulu Print API Integration
Description: Integrates with the Lulu Print API to create print jobs from WordPress.
Version: 1.0
Author: Umeunegbu Pascal
Author URI: https://umeunegbupascal.netlify.app/
*/

// Fire after payment is complete (on thank you page)
add_action('woocommerce_thankyou', 'lulu_send_print_job_after_payment', 10, 1);

function lulu_send_print_job_after_payment($order_id)
{
    if (!$order_id) {
        return;
    }

    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    // === MULTI-PRODUCT SUPPORT ===
    $supported_product_ids = [
        6924, 6870, 6800, 6795, 6791, 6759, 6752, 6743, 6735, 6623, 6615, 6340, 6310, 6109, 6107, 6105, 6100, 6898, 6878, 6864, 6347, 6567, 6701, 6672, 6642, 6860, 6832, 6917, 6908, 6914, 6905, 6601, 6765, 6927, 6353, 6421, 6405, 6398, 6810, 6821, 6839, 6847, 6325, 6319, 6589, 6583, 6826, 6851, 6789, 6784, 6632, 6562, 6557, 6549, 6539, 6536, 6531, 6529, 6524, 6576, 6728, 6717
    ];

    // Map product IDs to Lulu data (update URLs and package IDs as needed)
    $product_map = [
        6924 => [
            'title' => 'Blood Journal',
            'cover_url' => 'https://www.dropbox.com/scl/fi/j9q4gqi87lr179rc6d9zo/blood-journal-cover.pdf?rlkey=yolzzdmpsfmyre73807gdqnem&st=aq8ue94g&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/cvfq020bac8ia1w43coni/blood-journal-interior.pdf?rlkey=wxd7oovq1q3yh6zrq4icoo350&st=e92z0fvf&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],
        // Add your other products here, using the same structure.
        6870 => [
            'title' => 'Social Media Planner',
            'cover_url' => 'https://www.dropbox.com/scl/fi/k5pat40wimgxt2gqtkebm/book-cover-social-media-planner.pdf?rlkey=9b9248t9yk1xkgyyhru5yf4yt&st=pszt8tq9&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/bua5hs816psbh9pz3upr4/paperback-interior-social-media-planner.pdf?rlkey=bvikjway62j07bkj43b8q5j6n&st=jkme9hdp&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],
        6800 => [
            'title' => 'Home Cleaning',
            'cover_url' => 'https://www.dropbox.com/scl/fi/7guc9x69gsefv7bvjkvpp/book-cover-home-cleaning.pdf?rlkey=gg59cnnxnzvo14wy7i2eghg0m&st=vv12iiwr&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/dndetmos5m6k6vfqa3nod/paperback-interior-Home-cleaning.pdf?rlkey=rmfdfdutrxkp2gljxnkqqexcr&st=bvoizoo6&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],
        6795 => [
            'title' => 'Skin Care',
            'cover_url' => 'https://www.dropbox.com/scl/fi/7cntygp5hhz074n650e42/book-cover-skincare.pdf?rlkey=fvdh1lo4vfp1ct2wmdmj7xwcb&st=ogx6svee&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/wdoz0mnea0kemq0knqatq/paperback-interior-skincare.pdf?rlkey=2ndpbxxlhl30zltbkmtbxlrxt&st=2lydh71u&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],
        6791 => [
            'title' => 'Love',
            'cover_url' => 'https://www.dropbox.com/scl/fi/gnzkep71hhe0q097kmqg2/book-cover-self-love.pdf?rlkey=maihp3k5phvnlxhnoicnkwbbv&st=pm109dg0&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/2m20dculvns53wjp03dgg/paperback-interior-self-love.pdf?rlkey=yz5mjhaxo5y0jbn9dcwakosog&st=00oeh5lj&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],
        6759 => [
            'title' => 'Travel Checklist',
            'cover_url' => 'https://www.dropbox.com/scl/fi/tdx71xtpv1stq3uw64plo/book-cover-travel-checklist.pdf?rlkey=74vn9dhrwqutonx26nrkwtoq7&st=db8jfyee&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/2g32xbuuxkli10ykd673d/paperback-interior-travel-checklist.pdf?rlkey=i2qbt8lx2vo3tno8uh7fod3tm&st=x1yj8v0x&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],
        6752 => [
            'title' => 'Prayer and Fasting',
            'cover_url' => 'https://www.dropbox.com/scl/fi/mea6v2t6jl4ce6l7ejzj1/prayer-and-fasting-cover.pdf?rlkey=g3p7rfrnin49nopozxkqbogvm&st=htjfn7rc&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/joauzcws1cc2tqz7sa3xt/paperback-interior-Prayer-And-Fasting.pdf?rlkey=hcwifzdjaosum19185r4h5cea&st=uvuqu35r&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],
        6743 => [
            'title' => 'Personal Devotion',
            'cover_url' => 'https://www.dropbox.com/scl/fi/rwsd23496yuprkiz7s7pd/personal-devotion-cover.pdf?rlkey=lmrjb97wqra3y8iog4ud7xguq&st=k0d5xlhg&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/sebikmib3slv1bte485a9/paperbac-interior-personal-Devotion.pdf?rlkey=llw943ihnrk5lzzft5s005rhf&st=e8lnwa3o&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],
        6735 => [
            'title' => 'Sermon Notes',
            'cover_url' => 'https://www.dropbox.com/scl/fi/preluygz79719zk6jrj3x/sermon-notes-cover.pdf?rlkey=gi0tha6sxxra2o0w0ih6dpkzu&st=vi78dcxp&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/gdilcybv9rm4j78ev481h/paperback-interior-sermon-notes.pdf?rlkey=7i3oang4y3e4tljz601dbd3c9&st=9ti0m2vt&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],
        6623 => [
            'title' => 'Activity Book 4-6 Years',
            'cover_url' => 'https://www.dropbox.com/scl/fi/efdtn20l4cliuxjhr26ou/kids-activity-book-cover.pdf?rlkey=ny14sa0kzd1l1h0966iemtns4&st=ekuvmlr7&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/tvy39o03evkdme3axs025/paperback-interior-activity-book-4-6-years.pdf?rlkey=ziy4bd9ogui09j4qwfd9rhiaq&st=i5lolb4w&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],
        6615 => [
            'title' => 'Activity Book Pre School',
            'cover_url' => 'https://www.dropbox.com/scl/fi/zy38zn3vveuqcmun2i9aa/activity-book-pre-sch-cover.pdf?rlkey=fcg9xgka9tx4hp5oglsir6fsh&st=q7hsxawv&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/0dtm1ar28zut2e73cvvef/paperback-interior-activity-book-pre-school.pdf?rlkey=5iieuci3x95spwvcpm4w7hbii&st=x4jsoykb&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],
        6340 => [
            'title' => 'Kids Word Search',
            'cover_url' => 'https://www.dropbox.com/scl/fi/v3fde8y6ttdftykizjr5g/book-cover-kids-word-search.pdf?rlkey=2dg8ijgpftpswsgrt90pbs5kj&st=6pkd3hjo&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/2l4g0fwrql925jbcsugs6/Interior-kids-word-search.pdf?rlkey=zgrqj8t0hnx9aydwp2h1jnvsp&st=c4kjc6dm&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],
        6310 => [
            'title' => 'Bible Word Search',
            'cover_url' => 'https://www.dropbox.com/scl/fi/8u04kbyqpcn1b9sajjwd1/book-cover-BIBLE-word-search.pdf?rlkey=4ave1l6h84iyx33lyi0cqiv8k&st=1xud0k41&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/h25gsx76fsphs347ny1vw/Interior-Boble-Word-Search.pdf?rlkey=hjx4wzevpgxsi9puyras1m63v&st=rq65t4d2&dl=1',
            'pod_package_id' => '0600X0900BWPREPB080CW444MXX'
        ],
        6109 => [
            'title' => 'Travel Word Search',
            'cover_url' => 'https://www.dropbox.com/scl/fi/7fpddkr3k8tnqf1yits5z/book-cover-travel-word-search.pdf?rlkey=zi23p4t7s627k0mc8ofis4177&st=0stvwx1v&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/yoqonlcsoh830winxcvx6/Paperback-Interior-travel-word-search.pdf?rlkey=xwkuoy6531eovqs3ul41myrdm&st=iihnthpm&dl=1',
            'pod_package_id' => '0600X0900BWPREPB080CW444GXX'
        ],
        6107 => [
            'title' => 'Luxury Word Search',
            'cover_url' => 'https://www.dropbox.com/scl/fi/0h5kdrei7zgyzd801nw47/book-cover-luxury-word-search.pdf?rlkey=nxu3nlqaq7be55f1exwhsoptr&st=4lbs84aq&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/5bzg179zq1m1knzca51o8/Paperback-Interior-luxury-word-search.pdf?rlkey=nrh2hi4ioyxcnvugpuw0mc51x&st=negxov8o&dl=1',
            'pod_package_id' => '0600X0900BWPREPB080CW444GXX'
        ],
        6105 => [
            'title' => 'Love Word Search',
            'cover_url' => 'https://www.dropbox.com/scl/fi/wdiug43sh5m92f3jh1buu/newly-approved-love-cover-6-by-9.pdf?rlkey=9b351tzmizqlxbtso0h0zzaot&st=bzu02hja&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/1hvdygddh1kmncofymct0/Paperback-Interior-love-word-search.pdf?rlkey=qxbt9ahdna0rea35sy2bod10f&st=ubyc6ubu&dl=1',
            'pod_package_id' => '0600X0900BWPREPB080CW444GXX'
        ],
        6100 => [
            'title' => 'Dont Be Naughty',
            'cover_url' => 'https://www.dropbox.com/scl/fi/kplvih6w7loup5t1ahrmn/book-cover-dont-be-naughty.pdf?rlkey=tdmc1zizvac6gg5dsuuax9u3s&st=hnsji9a9&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/dvjh3zcbnimjqf5gs0whh/interior-dont-be-naughty.pdf?rlkey=y38m3rcskl3ox4mwunwk7mfee&st=lm705mxp&dl=1',
            'pod_package_id' => '0600X0900BWPREPB080CW444GXX'
        ],
        6898 => [
            'title' => 'Goal Tracker',
            'cover_url' => 'https://www.dropbox.com/scl/fi/fc0hi2w54rdyqwppa9gzm/goal-tracker-cover.pdf?rlkey=favw6evjcngyzpq3jhizuj5y4&st=ppkn66g6&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/tvcom8o0ldzwolh4t88wh/paperback-interior-Goals-Tracker.pdf?rlkey=3trs1jvsk2worqe3fkf2j3suv&st=8vtsgybx&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],
        6878 => [
            'title' => 'Weight Tracker',
            'cover_url' => 'https://www.dropbox.com/scl/fi/bkm54eyo18zfq5xejhti1/book-cover-weight-tracker.pdf?rlkey=umez4e46oqj9l52mz5vmpubsz&st=ngd986dp&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/g47zqm3vsmu4xxwpvewkc/paperback-interior-weight-tracker.pdf?rlkey=9mbhkb5nddad38hhjjjfo1fzw&st=wd43qcvc&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],
        6864 => [
            'title' => 'Budget Planner',
            'cover_url' => 'https://www.dropbox.com/scl/fi/pwsf3coj16th0rq3o5bze/budget-planner-cover.pdf?rlkey=cnathf3z5ho0sya34npswda0f&st=d86pizbh&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/yqdjb6e6l7u3wracxhdu6/paperback-interior-budgetplanner.pdf?rlkey=jjmbjjt7zfyggycb76k2ro0xs&st=53ya5u9m&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],
        6347 => [
            'title' => 'Kids Maze',
            'cover_url' => 'https://www.dropbox.com/scl/fi/m5mkvf2j468wqwxe5g77q/kids-maze.pdf?rlkey=8h4biu2blmm0qvllnmmwrouco&st=asrudfhc&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/fd7hvmvk7j2nsc8nhxaqy/kids-maze-interior.pdf?rlkey=01rl2wnnoqyse91jbzrv42qah&st=x9u8j9ql&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],
        6567 => [
            'title' => 'Yoruba Activity Book',
            'cover_url' => 'https://www.dropbox.com/scl/fi/w5d8txoi7yg16nh6wiqu0/book-cover-yoruba-book.pdf?rlkey=iswx3f4c03veodszbck1xavh3&st=5gligzlq&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/rubyk6xiw8m2ab59f7bx1/paperback-interior-yoruba-activity-book.pdf?rlkey=mv2wheunkdbe5av00ojy6sv8i&st=qpil6o90&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],

        6701 => [
            'title' => 'spot the diff advanced',
            'cover_url' => 'https://www.dropbox.com/scl/fi/bs7g67chtarazi6zofv0n/book-cover-spot-the-diff-advanced.pdf?rlkey=9wj9ick4d3ya6vee5dwm8rozn&st=21p6j1zz&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/4tkhg845twezx06g64qmn/paperback-interior-spot-the-diff-advanced.pdf?rlkey=9pxa1v58vugsfw6kt8cvn746q&st=yb6m8kfj&dl=1',
            'pod_package_id' => '0850X0850FCSTDPB060UW444GXX'
        ],

        6672 => [
            'title' => 'spot the difference mid',
            'cover_url' => 'https://www.dropbox.com/scl/fi/12oksz738bldpr6idv7kc/book-cover-spot-the-diff-mid.pdf?rlkey=svimt072uj982giy1i8qbup41&st=zc7amfyk&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/9fodgzzulkiwafc42h2v4/paperback-interior-spot-the-difference-mid.pdf?rlkey=pp4d1dx7xqmq2v2m7bqjk6xod&st=bko9bbme&dl=1',
            'pod_package_id' => '0850X0850FCSTDPB060UW444GXX'
        ],

        6642 => [
            'title' => 'spot the difference beginners',
            'cover_url' => 'https://www.dropbox.com/scl/fi/hqq6ujyhisimpw9vyb2z9/book-cover-spot-the-difference-beginners.pdf?rlkey=m0lohjyq2wjeq2kbknvj0dsca&st=ejeuf7w9&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/ceg1rqx26yyz9j65c4gak/paperback-interior-spot-the-difference-beginners.pdf?rlkey=u72gfp2716po765lvo9mme60q&st=zlyqpvr7&dl=1',
            'pod_package_id' => '0850X0850FCSTDPB060UW444GXX'
        ],

        6860 => [
            'title' => '3-in-1 meal planner',
            'cover_url' => 'https://www.dropbox.com/scl/fi/2du99zka6rz1oc7akwkgw/book-cover-mixed-meal-planner.pdf?rlkey=pwdu9lkzutriqsv6e4fv3mo8x&st=2va9qh7u&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/rdvn1jgl4k9h47dafzvv2/paperbck-interior-mixed-meal-planner.pdf?rlkey=zywtvn44cxesyj751mdt6hg2c&st=ujua35ms&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],

        6832 => [
            'title' => '3-in-1 planner',
            'cover_url' => 'https://www.dropbox.com/scl/fi/kvlvi9m9132m2l9xglesz/book-cover-3-in1-planner.pdf?rlkey=hi3bzlc6qw15fw3i2flyvdi1h&st=pdn2hshz&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/bpq5qf749dxyqho1i19bk/paperback-interior-3-in-1-planner-purchasehub-6-x-9-in.pdf?rlkey=avq929dez3hp8dvownkbg5t7g&st=35jbx36a&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],

        6917 => [
            'title' => '60 day gratitude challenge',
            'cover_url' => 'https://www.dropbox.com/scl/fi/hi5exq1fsbasjb7qggvwc/bookcover-gratitude-journal.pdf?rlkey=b04xspc73ltxm51d7ukyelfvq&st=wp4mdk37&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/0vrgiebj2jie0k58vwox4/interior-gratittude-notebook.pdf?rlkey=8qd2y0suxff7xi91thn2g2vs7&st=5map417d&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],

        6908 => [
            'title' => 'study plan',
            'cover_url' => 'https://www.dropbox.com/scl/fi/el4i19j572al0e730bjq6/book-cover-study-plan.pdf?rlkey=jwwxql6nrmobwplk1hzlbkwff&st=1lg4llrr&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/mlcqc0n0tm4uty6fslnba/paperback-interior-study-plan.pdf?rlkey=35zem3pkpizwqsj2n0ozxc9s8&st=n3bwcbl0&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],

        6914 => [
            'title' => 'reading log',
            'cover_url' => 'https://www.dropbox.com/scl/fi/xgbf28zx4ti5jpocjq55l/book-cover-reading-log.pdf?rlkey=3bhnsp0494zjo3rqjz1gylg6u&st=lslwn2xj&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/0ktyh91lzqzksml5f5oil/paperback-interior-reading-log.pdf?rlkey=xifmz1gekhlc3zvaxz9nd0sco&st=icjysvkq&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],

        6905 => [
            'title' => 'homework tracker',
            'cover_url' => 'https://www.dropbox.com/scl/fi/pzkim86r84ylo79ns15yw/book-cover-homework-tracker.pdf?rlkey=0m3ztu4g2zux8yt9cnb358wfi&st=3s2qchhi&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/h53506dghdo1l5qmh0q9o/paperback-interior-Homework-Tracker.pdf?rlkey=iomff41gnx4y92dc4wnpe6npt&st=o7bfxy70&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],

        6601 => [
            'title' => 'abc coloring book',
            'cover_url' => 'https://www.dropbox.com/scl/fi/g1ek8u3k7tnxv2dl0f6nx/book-cover-abc-coloring-book.pdf?rlkey=p7zdrzraod78bqiynqzixrpvg&st=sjzwapg0&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/rkr90qa08ys2fzwhba7ek/paperback-interior-abc-coloring-book.pdf?rlkey=fy8jsvu61pejzyrub5itfyar2&st=wyh8n9t6&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],

        6765 => [
            'title' => 'shopping checklist',
            'cover_url' => 'https://www.dropbox.com/scl/fi/xf4t5rw66kom2alurng88/book-cover-shopping-list.pdf?rlkey=c51rrw3u2r1yd6mevfvf5xy4b&st=zzzzxl4q&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/q0zftn8yhvs0dehrmdc8u/paperback-interior-shopping-list.pdf?rlkey=kb57ibl442ndqnifbvjjrmxrs&st=myof7nza&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],

        6927 => [
            'title' => 'baby record book',
            'cover_url' => 'https://www.dropbox.com/scl/fi/2yloqwgx8vp26m1lh5ht5/baby-book-cover.pdf?rlkey=hvmaywcndcphvxucm9j0iko1s&st=s9tobcm0&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/s66goit0yiupaxeo8y58v/interior-baby-book.pdf?rlkey=79earyk2pm358qkl8y71rvlld&st=roccdww2&dl=1',
            'pod_package_id' => '0850X0850FCSTDCW060UW444MXX'
        ],

        6353 => [
            'title' => 'adult maze',
            'cover_url' => 'https://www.dropbox.com/scl/fi/fdvm35stmn75qc6ufhjux/book-cover-adult-maze.pdf?rlkey=k933jxxe3hkgsf3tnbsfyf9ax&st=i59v3igx&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/sdt0cnasweyz8sbirepa5/Paperback-Interior-adult-maze.pdf?rlkey=6igcro7dk21yh92jj2moyv1xg&st=9tsv0e08&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],

        6421 => [
            'title' => 'sudoku advanced',
            'cover_url' => 'https://www.dropbox.com/scl/fi/t1lzju01l4sgcx569ygpt/book-cover-sudoku-advanced.pdf?rlkey=m0ezuli6u7rdb8tslkr3oin98&st=l41fbuo1&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/r1i7s08l662feny2b8mdb/Paperback-Interior-sudoku-advanced.pdf?rlkey=lg1e24wcyiydtomi472lai6uq&st=wkpbrris&dl=1',
            'pod_package_id' => '0600X0900BWPREPB080CW444GXX'
        ],

        6405 => [
            'title' => 'sudoku mid',
            'cover_url' => 'https://www.dropbox.com/scl/fi/3pglskg2gz9z0qaq4wzq9/book-cover-sudoku-mid.pdf?rlkey=dmhn3rcrm0zcfr06c12nfptap&st=e3oxpuyp&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/c8tdfyqv5n05hhvdfm3kt/Paperback-Interior-sudoku-mid.pdf?rlkey=6icx10ioudyovtbygoj4ejz8l&st=9cz7iox4&dl=1',
            'pod_package_id' => '0600X0900BWPREPB080CW444GXX'
        ],

        6398 => [
            'title' => 'sudoku beginners',
            'cover_url' => 'https://www.dropbox.com/scl/fi/7ptm5grw7eh84cu9blnr1/book-cover-sudoku-beginners.pdf?rlkey=24hv12oicdgoolc8t4mrousbg&st=irr4c3kf&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/udcporvq0k9jbj49hyxa6/Paperback-Interior-sudoku-beginners.pdf?rlkey=572cfhl5x3imuhm7pp9ualwn3&st=heogqkn4&dl=1',
            'pod_package_id' => '0600X0900BWPREPB080CW444GXX'
        ],

        6810 => [
            'title' => 'daily planner',
            'cover_url' => 'https://www.dropbox.com/scl/fi/mngtc6gg75su0d0xy2xhu/book-cover-daily-planner-and-diary.pdf?rlkey=8ntn4k1wdirk8mn0uj3tpotfk&st=bol3s33h&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/nm8ox5nnjy82avczm7qyl/paperback-interior-daily-planner.pdf?rlkey=jd4vvohbg6cemo8wh3q7n55ma&st=kxehg14r&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],

        6821 => [
            'title' => 'weekly planner',
            'cover_url' => 'https://www.dropbox.com/scl/fi/fezttopilwyzu8ejvfrxd/book-covr-weekly-planner.pdf?rlkey=wnp5ism5axt7ezmufplpxfapm&st=7bh4uo0z&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/qeenho17aak86caqamwnv/paperback-interior-weekly-planner-and-dossier.pdf?rlkey=r31xqncw1ddhuejqxbd4p2z8z&st=i7c7tulk&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],

        6839 => [
            'title' => 'daily meal planner',
            'cover_url' => 'https://www.dropbox.com/scl/fi/9jdtyiwnbedpjiutheoae/book-cover-daily-meal-planner.pdf?rlkey=g9fg2wawq34o947hbikpi7cqg&st=7epmi9l5&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/m7z9mbxulsyc5bbizqrcd/paperbackback-interior-daily-meal-planner.pdf?rlkey=k3tjum1l44t4wzvycif2n8x7g&st=zpis4qrq&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],

        6847 => [
            'title' => 'weekly meal planner',
            'cover_url' => 'https://www.dropbox.com/scl/fi/ht4d92ixjc5tcqrori8eu/book-cover-weekly-Meal-Planner.pdf?rlkey=3j0qeoaxjm3t777gpm2qfge6q&st=6peavpln&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/dfpc9xq9zcukkwb0skftu/paperback-interior-weekly-meal-planner.pdf?rlkey=8amqzqe4wih6gazsq76fw7loa&st=hoelv175&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],

        6325 => [
            'title' => 'Easter word search',
            'cover_url' => 'https://www.dropbox.com/scl/fi/5bdfgfaxiqcm8zh6kil1t/book-cover-Easter-word-search.pdf?rlkey=gsd5w6excjhwa8xg6yrlar063&st=jfyrjjq1&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/ovsf4vhtxlsw9kdgczwa7/Interior-Easter-word-search.pdf?rlkey=6whgnlkb78q0rujcxa1mlfgsx&st=jb6dmz7c&dl=1',
            'pod_package_id' => '0600X0900BWPREPB080CW444GXX'
        ],

        6319 => [
            'title' => 'xmas word search',
            'cover_url' => 'https://www.dropbox.com/scl/fi/m0cy50h2z6ch7ka7hdcxf/book-cover-xmas-word-search.pdf?rlkey=l3deqdzpx1jh6rswriy19bkkw&st=fk5qvqi5&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/zja57glbg9msk3uavi6st/Paperback-Interior-Xmas-word-search.pdf?rlkey=73p3xfucpa2vrohkvqgk23u86&st=px61zlf9&dl=1',
            'pod_package_id' => '0600X0900BWPREPB080CW444GXX'
        ],

        6589 => [
            'title' => 'Easter coloring book',
            'cover_url' => 'https://www.dropbox.com/scl/fi/2jwa89027r97zmeofue4g/book-cover-easter-coloring-book.pdf?rlkey=qty98nzdxlko3mab3h4jzhfow&st=72mhwagx&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/t2algwldpkli1x2zaiwqy/paperback-interior-Easter-Coloring-Book.pdf?rlkey=lp3pbl36n40htzl5nux9ouxk0&st=pxhj5c93&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],

        6583 => [
            'title' => 'xmas coloring book',
            'cover_url' => 'https://www.dropbox.com/scl/fi/650dlls4997ww3zr07l88/book-cover-Christmas-Coloring-Book.pdf?rlkey=qx8m6iz4a8d2ao0nt15ev9m53&st=80enr8h9&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/igcoiysmc3wl4cwd2unao/paperback-interior-Christmas-Coloring-Book.pdf?rlkey=i826gw4jbldznmeu03xc71qtz&st=so2skrrs&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],

        6826 => [
            'title' => 'monthly planner',
            'cover_url' => 'https://www.dropbox.com/scl/fi/poomflsbfcsyygkmpw83h/book-cover-monthly-planner.pdf?rlkey=krsgil13cd1cxli6n0mcuc8cm&st=31fj8v85&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/p2005izz7ysskxdtdti50/paperback-interior-Monthly-Planner-And-journal.pdf?rlkey=59jr84fck4q0gvs5kf6ndtvog&st=i0csaluy&dl=1',
            'pod_package_id' => '0600X0900BWSTDPB060UW444GXX'
        ],

        6851 => [
            'title' => 'monthly meal planner',
            'cover_url' => 'https://www.dropbox.com/scl/fi/7tol5nyantijrfmyyf4t1/book-cover-monthly-meal-planner-17.382-x-11.25-in.pdf?rlkey=0vi9is0uks0eohj16g2yy60m1&st=s9kjuzxg&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/yvyyw327jded5v0cx0wo6/paperback-interior-monthly-meal-planner.pdf?rlkey=h6woajiyru5w5yoj4l822wg2p&st=2jf19cdc&dl=1',
            'pod_package_id' => '0850X1100BWSTDPB060UW444GXX'
        ],

        6789 => [
            'title' => 'baby and mom checklist',
            'cover_url' => 'https://www.dropbox.com/scl/fi/mnt8io32w9vt6j7202ubi/book-cover-Baby-Mom-Hospital-Checklist.pdf?rlkey=wctdo8o56zc6m582rl17imxh3&st=hrvuld9l&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/oyl7oib8cbjiwh1c972x5/paperback-interior-baby-and-mom-checklist.pdf?rlkey=lbsk99ab3ekj51r2vuv7c77ef&st=479799d8&dl=1',
            'pod_package_id' => '0600X0900FCPRESS080CW444GXX'
        ],

        6784 => [
            'title' => 'wedding checklist',
            'cover_url' => 'https://www.dropbox.com/scl/fi/95fhmr8ms0t5qa70id2t9/book-cover-wedding-checklist.pdf?rlkey=avaegolz14jykdlroc3cyof7v&st=y5k2bwgf&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/gybe54kqmlhtyk7pkjddx/paperback-interior-wedding-checklist.pdf?rlkey=ikpezhqqrdq13bubut3sqoc5n&st=dh6qkdvk&dl=1',
            'pod_package_id' => '0600X0900FCPRESS080CW444GXX'
        ],

        6632 => [
            'title' => 'affirmations',
            'cover_url' => 'https://www.dropbox.com/scl/fi/90a9bmixapw1kthh7j2xk/book-cover-affirmations.pdf?rlkey=okdxkzireuucqio8gyti7u94c&st=553s3ay3&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/czw1dnig1ubpr3b25opat/paperback-interior-affirmations.pdf?rlkey=3k6dp8v9zv4en6mssl9rspsvk&st=yljrrhgb&dl=1',
            'pod_package_id' => '0850X0850FCSTDPB060UW444GXX'
        ],

        6562 => [
            'title' => 'honest turtle',
            'cover_url' => 'https://www.dropbox.com/scl/fi/izcwn2j50ug8hhso9ga7f/book-cover-honest-turtle.pdf?rlkey=q0bfazcw5thgttrcqdg0qpwjt&st=i553gjnh&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/jn1wv45q7w3a5s0sogbmh/paperback-interior-The-Honest-Turtle.pdf?rlkey=csrap0zkkuxuhs23hxycnf8o9&st=3blrmji3&dl=1',
            'pod_package_id' => '0850X0850FCSTDPB060UW444GXX'
        ],

        6557 => [
            'title' => 'rainbow',
            'cover_url' => 'https://www.dropbox.com/scl/fi/100kyjajwjkcmnzqlf6d3/book-cover-rainbow-story.pdf?rlkey=uhcha5iv0whvty2k8vlbya31i&st=kn51vv3b&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/4p0mt9doxz7onv9u4glp0/paperback-interior-rainbow-story.pdf?rlkey=z70mgvyfy0bktx487ou59htjc&st=dhqtnpoo&dl=1',
            'pod_package_id' => '0850X0850FCSTDPB060UW444GXX'
        ],

        6549 => [
            'title' => 'grateful seed',
            'cover_url' => 'https://www.dropbox.com/scl/fi/1x6pqow49jy8wnxh3mmug/book-cover-grateful-seed.pdf?rlkey=5dhe75ahlhajunq8oub6pa7ve&st=ni9rnh1i&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/xpfgbovh5ijc3tpso8pk5/paperback-interior-The-Grateful-Little-Seed.pdf?rlkey=0hiy0y7mdpjjs47ynoi5wvfeg&st=io1baqa9&dl=1',
            'pod_package_id' => '0850X0850FCSTDPB060UW444GXX'
        ],

        6539 => [
            'title' => 'dream train',
            'cover_url' => 'https://www.dropbox.com/scl/fi/0t18i0wiudd7lkecmdf3i/book-cover-dream-train.pdf?rlkey=3nbxe4n84a73iewgcmpuer6ar&st=34g0cnmk&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/aycq5ln7oxe7yx5ang3sq/paperback-interior-the-Dream-Train.pdf?rlkey=56t52vpj2jmjcg4m0vuovnky2&st=8yj67gz3&dl=1',
            'pod_package_id' => '0850X0850FCSTDPB060UW444GXX'
        ],

        6536 => [
            'title' => 'duckling',
            'cover_url' => 'https://www.dropbox.com/scl/fi/0zwcd9g67vp70pusnxl7f/book-cover-duckling.pdf?rlkey=mixqw6ju4wklehayz6ob0hsar&st=1lwr7mny&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/wt193qiyet9clr1uhn8sb/Paperback-Interior-Duckling.pdf?rlkey=r49uyu0ck4hqnl863mo9tudbk&st=ft37pknm&dl=1',
            'pod_package_id' => '0850X0850FCSTDPB060UW444GXX'
        ],

        6531 => [
            'title' => 'magical pajams',
            'cover_url' => 'https://www.dropbox.com/scl/fi/2hpttp2bmwkivtn0yi325/purcgasehub-pajama-book-cover-17.382-x-8.75-in.pdf?rlkey=9x1fjdnxesxj3586rf5avegvv&st=l3f2p96r&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/hw95mn3m43txc884ydad2/Copy-of-approved-Paperback-Interior-Magical-Pajamas-thepurchasehub-8.75-x-8.75-in.pdf?rlkey=3z7oifz0ye6i1s2s8g6q1y94g&st=7tzumk2y&dl=1',
            'pod_package_id' => '0850X0850FCSTDPB060UW444GXX'
        ],

        6529 => [
            'title' => 'wise star',
            'cover_url' => 'https://www.dropbox.com/scl/fi/nld1xtgndpstx75balcyn/book-cover-wise-star.pdf?rlkey=h6vjs9ckpnfal4pr8bjokhzcj&st=wny246u7&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/ns5xh9rqr0g43p9zpb7tc/wise-star-interior.pdf?rlkey=htf0ih13g8y432fp7io08r2lg&st=m97v0heh&dl=1',
            'pod_package_id' => '0850X0850FCSTDPB060UW444GXX'
        ],

        6524 => [
            'title' => 'leo and moonlight',
            'cover_url' => 'https://www.dropbox.com/scl/fi/evbtxjmaw0gxr7ezg9boy/book-cover-leo-and-the-moonligh.pdf?rlkey=6kophoynm4oo515v0j86mtxy6&st=mqm05084&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/14vw2dhxipnsr5l1jaev6/Paperback-Interior-leo-and-the-moonlight-story-purchasehub-8.75-x-8.75-in.pdf?rlkey=6dak1u0pn1w4n9h9qqs87de4e&st=s57f50n9&dl=1',
            'pod_package_id' => '0850X0850FCSTDPB060UW444GXX'
        ],

        6576 => [
            'title' => 'riddles and jokes',
            'cover_url' => 'https://www.dropbox.com/scl/fi/qof4abf14t93pnsgvwdkk/book-cover-Fun-Riddles-and-witty-jokes.pdf?rlkey=d7sx2tph0lbn4pbl63c8655la&st=ide2ipru&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/wnq3ojcxe90d7o3w0zmp5/paperback-interior-Riddles-And-Jokes.pdf?rlkey=chsfwufbsje6j9rml4as9xpz3&st=u5sbvw7l&dl=1',
            'pod_package_id' => '0850X0850BWSTDPB060UW444GXX'
        ],

        6728 => [
            'title' => 'Easter story',
            'cover_url' => 'https://www.dropbox.com/scl/fi/s6rs1kmv31jzxjthgsnk4/book-cover-Easter-story.pdf?rlkey=ylglazjlt3spglbj4dbaby8i2&st=9gq8ekt9&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/0hrg44jmr5wssrmqbsc7a/paperback-interior-Easter-Story.pdf?rlkey=4mcak0pblflhugh70ojkc6avr&st=sf9t2mqd&dl=1',
            'pod_package_id' => '0850X0850FCPRESS080CW444GXX'
        ],

        6717 => [
            'title' => 'xmas story',
            'cover_url' => 'https://www.dropbox.com/scl/fi/x1r7c7g978gl1hbtmg287/book-cover-story-of-JESUS.pdf?rlkey=nqk59cxqqn4qoi7vi92kf4wrs&st=69wwqbe6&dl=1',
            'interior_url' => 'https://www.dropbox.com/scl/fi/mf3izze7n3r67al1cq4zi/paperback-interior-story-Of-JESUS.pdf?rlkey=ugp1hpwugi71ztq2whoq8e9b1&st=lif1o8kh&dl=1',
            'pod_package_id' => '0850X0850FCPRESS080CW444GXX'
        ]
    ];

    $line_items = [];
    foreach ($order->get_items() as $item) {
        $product_id = (int)$item->get_product_id();
        if (in_array($product_id, $supported_product_ids, true) && isset($product_map[$product_id])) {
            $quantity = $item->get_quantity();
            $product = wc_get_product($product_id);
            $custom_page_count = $product ? $product->get_meta('page_count') : '';
            $page_count = (is_numeric($custom_page_count) && (int)$custom_page_count > 0) ? (int)$custom_page_count : 120;

            $map = $product_map[$product_id];

            $line_items[] = [
                'external_id' => 'item-' . $product_id . '-' . $item->get_id(),
                'cover' => [
                    'source_url' => $map['cover_url']
                ],
                'interior' => [
                    'source_url' => $map['interior_url']
                ],
                'pod_package_id' => $map['pod_package_id'],
                'quantity' => $quantity,
                'title' => $map['title']
                // Optionally add 'page_count' => $page_count if Lulu API supports it
            ];
        }
    }

    if (empty($line_items)) {
        return;
    }

    // === STEP 1: Get Access Token ===
    $clientId = defined('LULU_CLIENT_ID') ? LULU_CLIENT_ID : '';
    $clientSecret = defined('LULU_CLIENT_SECRET') ? LULU_CLIENT_SECRET : '';

    $authHeader = base64_encode("$clientId:$clientSecret");
    $tokenUrl = 'https://api.lulu.com/auth/realms/glasstree/protocol/openid-connect/token';
    // $tokenUrl = 'https://api.sandbox.lulu.com/auth/realms/glasstree/protocol/openid-connect/token';


    $tokenHeaders = [
        'Authorization: Basic ' . $authHeader,
        'Content-Type: application/x-www-form-urlencoded'
    ];

    $tokenData = http_build_query([
        'grant_type' => 'client_credentials'
    ]);

    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $tokenHeaders);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $tokenData);

    $tokenResponse = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log('Token request error: ' . curl_error($ch));
        curl_close($ch);
        return;
    }
    curl_close($ch);

    $tokenJson = json_decode($tokenResponse, true);

    if (!isset($tokenJson['access_token'])) {
        error_log('Failed to get access token: ' . $tokenResponse);
        return;
    }

    $accessToken = $tokenJson['access_token'];

    // === STEP 2: Prepare Shipping Address ===
    $shipping = $order->get_address('shipping');
    if (empty($shipping['address_1']) || empty($shipping['city']) || empty($shipping['postcode']) || empty($shipping['country'])) {
        $shipping = $order->get_address('billing');
    }

    $shipping = array_merge([
        'first_name' => '',
        'last_name' => '',
        'address_1' => '',
        'city' => '',
        'state' => '',
        'postcode' => '',
        'country' => ''
    ], $shipping);

    $name = trim($shipping['first_name'] . ' ' . $shipping['last_name']);
    if (empty($name)) {
        $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
    }
    if (empty(trim($name))) {
        $name = 'Customer';
    }

    $phone = method_exists($order, 'get_shipping_phone') ? $order->get_shipping_phone() : '';
    if (empty($phone)) {
        $phone = $order->get_billing_phone();
    }
    if (empty($phone)) {
        $phone = '0000000000';
    }

    // === STEP 3: Get selected shipping level from order meta ===
    $shipping_level = 'GROUND'; // fallback
    $shipping_methods = $order->get_shipping_methods();
    foreach ($shipping_methods as $shipping_item) {
        $meta_data = $shipping_item->get_meta_data();
        foreach ($meta_data as $meta) {
            if ($meta->key === 'lulu_shipping_level') {
                $shipping_level = $meta->value;
                break 2;
            }
        }
    }

    // === STEP 4: Create Print Job ===
    $printJobUrl = 'https://api.lulu.com/print-jobs';
    // $printJobUrl = 'https://api.sandbox.lulu.com/print-jobs';

    $printJobData = [
        'contact_email' => 'support@thepurchasehub.com',
        'external_id' => 'ORDER-' . $order_id,
        'production_delay' => 120,
        'line_items' => $line_items,
        'shipping_address' => [
            'name'         => $name,
            'street1'      => $shipping['address_1'],
            'city'         => $shipping['city'],
            'state_code'   => $shipping['state'],
            'postcode'     => $shipping['postcode'],
            'country_code' => $shipping['country'],
            'phone_number' => $phone
        ],
        'shipping_level' => $shipping_level
    ];

    // Log the shipping address and shipping level for debugging
    error_log('Lulu shipping payload: ' . print_r($printJobData['shipping_address'], true));
    error_log('Lulu shipping level: ' . $printJobData['shipping_level']);

    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    $ch = curl_init($printJobUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($printJobData));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log('cURL error: ' . curl_error($ch));
    } else {
        error_log("Response from Lulu: $response");
    }

    curl_close($ch);

    $response_data = json_decode($response, true);
    if (isset($response_data['id'])) {
        update_post_meta($order_id, '_lulu_print_job_id', $response_data['id']);
        error_log("Print job response: " . print_r($response_data, true));
        error_log("Order ID at print job creation: " . print_r($order_id, true));
        error_log("Saved _lulu_print_job_id {$response_data['id']} for order {$order_id}");

    }
}

// Send tracking email
function lulu_send_tracking_email($order_id, $tracking_url, $carrier, $tracking_id)
{
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }
    $to = $order->get_billing_email();
    $subject = 'Your Order Has Shipped - Tracking Info';
    $message = "Hello,\n\nYour order has shipped!\n\nCarrier: $carrier\nTracking ID: $tracking_id\nTrack your shipment: $tracking_url\n\nThank you for shopping with us!";
    wp_mail($to, $subject, $message);
}

// WooCommerce thank you page message
add_action('woocommerce_thankyou', function ($order_id) {
    echo '<p>You will receive an email with tracking details once your items have shipped.</p>';
});

// Show tracking info on order details page
add_action('woocommerce_order_details_after_order_table', function ($order) {
    $tracking_url = get_post_meta($order->get_id(), '_lulu_tracking_url', true);
    $carrier = get_post_meta($order->get_id(), '_lulu_carrier_name', true);
    $tracking_id = get_post_meta($order->get_id(), '_lulu_tracking_id', true);
    if ($tracking_url && $carrier && $tracking_id) {
        echo '<p><strong>Tracking:</strong> <a href="' . esc_url($tracking_url) . '" target="_blank">' . esc_html($carrier) . '</a> (ID: ' . esc_html($tracking_id) . ')</p>';
    }
});

// Add tracking info to completed order email
add_action('woocommerce_email_order_details', function ($order, $sent_to_admin, $plain_text, $email) {
    if ($email->id === 'customer_completed_order') {
        $tracking_url = get_post_meta($order->get_id(), '_lulu_tracking_url', true);
        $carrier = get_post_meta($order->get_id(), '_lulu_carrier_name', true);
        $tracking_id = get_post_meta($order->get_id(), '_lulu_tracking_id', true);
        if ($tracking_url && $carrier && $tracking_id) {
            echo '<p><strong>Tracking Details:</strong><br>';
            echo 'Carrier: ' . esc_html($carrier) . '<br>';
            echo 'Tracking ID: ' . esc_html($tracking_id) . '<br>';
            echo 'Track your shipment: <a href="' . esc_url($tracking_url) . '" target="_blank">' . esc_html($tracking_url) . '</a></p>';
        }
    }
}, 20, 4);

// Lulu webhook endpoint for print job status changes
add_action('rest_api_init', function () {
    register_rest_route('lulu/v1', '/webhook/', [
        'methods' => 'POST',
        'callback' => 'lulu_webhook_handler',
        'permission_callback' => '__return_true'
    ]);
});

function lulu_webhook_handler($request)
{
    error_log('Webhook triggered');
    $body = $request->get_json_params();
    error_log('Webhook payload: ' . print_r($body, true));

    // Check for shipped status
    if (
        !empty($body['data']['status']['name']) &&
        $body['data']['status']['name'] === 'SHIPPED' &&
        !empty($body['data']['id'])
    ) {
        $order_id = lulu_find_order_by_print_job_id($body['data']['id']);
        error_log('Found order ID: ' . print_r($order_id, true));
        if ($order_id && !empty($body['data']['line_items'])) {
            foreach ($body['data']['line_items'] as $item) {
                if (
                    !empty($item['status']['name']) &&
                    $item['status']['name'] === 'SHIPPED' &&
                    !empty($item['status']['messages']['tracking_urls'])
                ) {
                    update_post_meta($order_id, '_lulu_tracking_id', $item['status']['messages']['tracking_id']);
                    update_post_meta($order_id, '_lulu_tracking_url', $item['status']['messages']['tracking_urls'][0]);
                    update_post_meta($order_id, '_lulu_carrier_name', $item['status']['messages']['carrier_name']);
                    lulu_send_tracking_email(
                        $order_id,
                        $item['status']['messages']['tracking_urls'][0],
                        $item['status']['messages']['carrier_name'],
                        $item['status']['messages']['tracking_id']
                    );
                    $order = wc_get_order($order_id);
                    if ($order && $order->get_status() !== 'completed') {
                        $order->update_status('completed', 'Order marked completed because Lulu item shipped (webhook).');
                    }
                }
            }
        }
    }
    return ['success' => true];
}

// Helper: Find WooCommerce order by Lulu print_job_id
function lulu_find_order_by_print_job_id($print_job_id)
{
    global $wpdb;
    $order_id = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_lulu_print_job_id' AND meta_value = %s LIMIT 1",
        $print_job_id
    ));
    return $order_id ? intval($order_id) : false;
}
