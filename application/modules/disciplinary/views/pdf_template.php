<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Notice to Explain (NTE) — Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        /* Repeat header on each printed page */

        #print-header {
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            height: 75px;
            background: #fff;
            padding-bottom: 15px;
        }

        #print-content {
            margin-top: 95px !important;
        }

        @media print {
            #print-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 9999;
                padding: 0;
                height: auto;
            }

            .print-spacer {
                display: block;
                height: 36mm;
            }

            #print-content {
                margin-top: 0 !important;
            }
        }

        @media screen {
            .print-spacer {
                display: none;
            }
        }
    </style>
</head>

<body style="margin:0; padding:0; background:#ffffff; color:#000000; font-family: Arial, Helvetica, sans-serif;">
    <!-- GLOBAL PRINT HEADER (renders once in HTML, repeats on every print page) -->
    <div id="print-header" style="background:#ffffff; border-bottom:0 none;">
        <div style="width:210mm; max-width:210mm; margin:0 auto; padding:0; box-sizing:border-box;">
            <table style="width:100%; border-collapse:collapse; margin:0;">
                <tr>
                    <td style="width:46mm; vertical-align:top; padding-left: 10px;">
                        <img src="<?= base_url('dist/img/main-logo.png') ?>" alt="Company Logo" style="display:block; max-width:42mm; height:auto;" />
                    </td>
                    <td style="text-align:center; vertical-align:middle;">
                        <div style="font-size:33px; line-height:33px; font-weight:bold; font-family: Bookman Old Style;">IDEAL TECH STAFFING</div>
                        <div style="font-size:20px; line-height:20px; font-family: serif;">
                            451 State Street, Unit C North Haven, CT 06473<br />
                            <span>www.idealtechstaffing.com</span>
                        </div>
                    </td>
                    <td style="width:16mm;"></td>
                </tr>
            </table>
        </div>
    </div>
    <!-- PAGE WRAPPER -->
    <div style="width: 210mm; max-width: 210mm; min-height: 297mm; margin: 0 auto; padding: 0; box-sizing: border-box;" id="print-content">

        <!-- HEADER (handled globally) -->
        <div class="print-spacer"></div>

        <!-- FORM TITLE -->
        <div style="text-align:center; font-weight:bold; font-size:14px; margin-bottom:3mm;">
            NOTICE TO EXPLAIN (NTE) FORM v0.1
        </div>

        <!-- DETAILS GRID -->
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:9mm; table-layout:fixed;">
            <colgroup>
                <col style="width:22%" />
                <col style="width:28%" />
                <col style="width:22%" />
                <col style="width:28%" />
            </colgroup>
            <tr>
                <td style="padding:6px 8px; border:1px solid #000; font-weight:bold;">Employee Name:</td>
                <td style="padding:6px 8px; border:1px solid #000;"><?= !empty($employee_name) ? $employee_name : '--' ?></td>
                <td style="padding:6px 8px; border:1px solid #000; font-weight:bold;">Account Name:</td>
                <td style="padding:6px 8px; border:1px solid #000;"> <?= !empty($account) ? $account : '--' ?></td>
            </tr>
            <tr>
                <td style="padding:6px 8px; border:1px solid #000; font-weight:bold;">Designation:</td>
                <td style="padding:6px 8px; border:1px solid #000;"><?= !empty($emp_level) ? $emp_level : '---' ?></td>
                <td style="padding:6px 8px; border:1px solid #000; font-weight:bold;">Date:</td>
                <td style="padding:6px 8px; border:1px solid #000;"> <?= !empty($nte_date) ? date('F j, Y', strtotime($nte_date)) : date('F j, Y') ?> </td>
            </tr>
            <tr>
                <td style="padding:6px 8px; border:1px solid #000; font-weight:bold;">Manager/Supervisor:</td>
                <td style="padding:6px 8px; border:1px solid #000;"><?= !empty($lead_name) ? $lead_name : '--' ?></td>
                <td style="padding:6px 8px; border:1px solid #000; font-weight:bold;">NTE/NOD Entry Log No.</td>
                <td style="padding:6px 8px; border:1px solid #000;">
                    <?= !empty($id) ? str_pad($id, 3, '0', STR_PAD_LEFT) : '00' ?>
                </td>
            </tr>
        </table>

        <!-- RE LINE -->
        <div style="font-weight:bold; font-size:14px; margin-bottom:5mm;">RE: <?= !empty($violation) ? $violation : '' ?></div>

        <!-- BODY -->
        <div style="font-size:12px; line-height:1.55; text-align:justify; margin-bottom:5mm;"> <?= !empty($violation_details) ? removeInlineStyles($violation_details) : '' ?> </div>

        <div style="font-size:12px; line-height:1.55; text-align:justify; margin-bottom:5mm;">
            Please be reminded that you are evaluated on your overall job performance by means of not only output in tasks assigned, but also your compliance to company policies.
        </div>

        <div style="font-size:12px; line-height:1.55; text-align:justify; margin-bottom:6mm;">
            You are given <span style="font-weight:bold;">48 hours</span> to answer this offense and explain in detail your side why you have committed such infraction. Your answer will help us immensely evaluate your present job and its continuity here in Ideal Tech Staffing Phils. As with others who were given the opportunity to air their side, this will be your chance to rebut any information indicated in this document.
        </div>

        <div style="font-size:12px; line-height:1.55; text-align:justify; margin-bottom:10mm;">
            By affixing your signature in the conforme below, you are acknowledging that you understand each statement herein and the offense that has been hurled against your person, and that you agree to comply with our request.
        </div>

        <div style="font-weight:bold; font-size:13px; margin-bottom:2mm; font-style:italic; margin-top: 60px;">Sincerely yours,</div>

        <!-- SIGNATORIES -->
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-top:8mm; margin-bottom:14mm;">
            <tr>
                <td style="width:60%; padding:0; vertical-align:top;">
                    <img src="<?= base_url('dist/img/frances-signature.png') ?>" alt="Signature" style="display: block; max-width: 55mm; max-height: 34mm; height: auto; margin: -20px 0 -50px -20px;" />
                    <div style="font-weight:bold; font-size:14px; font-style:italic;">Frances G. Manatad</div>
                    <div style="font-weight:bold; font-size:12px; font-style:italic;">Operations Manager</div>
                </td>
            </tr>
        </table>


    </div>

    <!-- PAGE 2 -->
    <div style="page-break-before:always;"></div>
    <div style="width: 210mm; max-width: 210mm; min-height: 297mm; margin: 0 auto; padding: 0; box-sizing: border-box;">

        <!-- HEADER (handled globally) -->
        <div class="print-spacer"></div>

        <!-- EMPLOYEE EXPLANATION -->
        <div style="font-size:12px; margin-bottom:4mm;">
            <span style="font-weight:bold;">Employee’s Explanation</span>
            <span style="font-style:italic;"> (Please explain your side on why the above ground and corresponding sanction will not be given due course):</span>
        </div>

        <!-- Writing area (intentionally blank space) -->
        <div style="height:60mm;">
            <div style="font-size:12px; line-height:1.55; text-align:justify; margin-bottom:5mm;"> <?= !empty($employee_explanation) ? removeInlineStyles($employee_explanation) : '' ?> </div>
        </div>

        <!-- Employee signature/date -->
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-top:2mm; margin-bottom:7mm;">
            <tr>
                <td style="width:50%; padding-right:8mm; vertical-align:bottom;">Date:__________________</td>
                <td style="width:50%; text-align:right; vertical-align:bottom;">
                    <div style="display:inline-block; width:70%; border-top:1px solid #000; height:0; margin-bottom:2mm;"></div><br />
                    <span>Signature Over Printed Name of Employee</span>
                </td>
            </tr>
        </table>

        <!-- Instruction paragraph -->
        <div style="font-size:11.5px; line-height:1.5; text-align:justify; margin-bottom:9mm;">
            (You may use a separate page as continuation of your explanation above. Please make sure to affix the above signature on top of the affixing of your signature over your printed name at the end of your statement in the subsequent page as well. Submit this to the Superior within 5 days from service thereof. Your Superior will make a copy thereof and will return this same form to you with the Notice of Decision below accomplished. The Superior will log the file to HR 201 File.) <span style="color:#9d0000; font-style:italic;">Hearing may be called.</span>
        </div>

        <!-- NOD TITLE -->
        <div style="text-align:center; font-weight:bold; font-size:13px; margin-bottom:2mm;">NOTICE OF DECISION (NOD)</div>
        <div style="text-align:center; font-size:11.5px; font-style:italic; margin-bottom:4mm;">
            (To be filled-out by the direct superior. An appeal of this decision shall be made within 5 days from service thereof)
        </div>

        <!-- Decision writing lines -->
        <div style="height:60mm;">
            <div style="font-size:12px; line-height:1.55; text-align:justify; margin-bottom:5mm;"> <?= !empty($notice_of_decision) ? removeInlineStyles($notice_of_decision) : '' ?> </div>
        </div>

        <!-- Superior signature/date -->
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-top:2mm; margin-bottom:10mm;">
            <tr>
                <td style="width:50%; padding-right:8mm; vertical-align:bottom;">Date:__________________</td>
                <td style="width:50%; text-align:right; vertical-align:bottom;">
                    <div style="display:inline-block; width:70%; border-top:1px solid #000; height:0; margin-bottom:2mm;"></div><br />
                    <span>Signature Over Printed Name of Superior</span>
                </td>
            </tr>
        </table>

        <!-- SANCTIONS SIDE-BY-SIDE -->
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:50%; vertical-align:top; text-align:center; padding-right:10px;">
                    <div style="font-weight:bold; margin-bottom:4px;">Sanction Incurred</div>
                    <table style="width:100%; margin:0 auto; border-collapse:collapse; font-size:9px; table-layout:fixed;">
                        <tr>
                            <th style="border:1px solid #000; padding:6px; background:#7a1f1f; color:#fff;">Offense Level</th>
                            <th style="border:1px solid #000; padding:6px; background:#7a1f1f; color:#fff;">1st Offense</th>
                            <th style="border:1px solid #000; padding:6px; background:#7a1f1f; color:#fff;">2nd Offense</th>
                            <th style="border:1px solid #000; padding:6px; background:#7a1f1f; color:#fff;">3rd Offense</th>
                            <th style="border:1px solid #000; padding:6px; background:#7a1f1f; color:#fff;">4th Offense</th>
                            <th style="border:1px solid #000; padding:6px; background:#7a1f1f; color:#fff;">Cleansing Period</th>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000; padding:6px; font-weight:bold;">Minor Offenses</td>
                            <td style="border:1px solid #000; padding:6px;">Documented Verbal Warning</td>
                            <td style="border:1px solid #000; padding:6px;">Written Warning</td>
                            <td style="border:1px solid #000; padding:6px;">Final Warning</td>
                            <td style="border:1px solid #000; padding:6px;">Dismissal</td>
                            <td style="border:1px solid #000; padding:6px;">One (1) year</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000; padding:6px; font-weight:bold;">Less Grave Offenses</td>
                            <td style="border:1px solid #000; padding:6px;">First Written Warning</td>
                            <td style="border:1px solid #000; padding:6px;">Final Warning</td>
                            <td style="border:1px solid #000; padding:6px;">Dismissal</td>
                            <td style="border:1px solid #000; padding:6px;"></td>
                            <td style="border:1px solid #000; padding:6px;">Two (2) years</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000; padding:6px; font-weight:bold;">Grave Offenses</td>
                            <td style="border:1px solid #000; padding:6px;">Dismissal</td>
                            <td style="border:1px solid #000; padding:6px;"></td>
                            <td style="border:1px solid #000; padding:6px;"></td>
                            <td style="border:1px solid #000; padding:6px;"></td>
                            <td style="border:1px solid #000; padding:6px; background:#e0e0e0;">Not Applicable</td>
                        </tr>
                    </table>
                    <div style="font-size:9px; font-style:italic; margin-top:4px;">(Superior will encircle correspondingly)</div>
                </td>

                <td style="width:50%; vertical-align:top; text-align:center; padding-left:10px;">
                    <div style="font-weight:bold; margin-bottom:4px;">Sanction to Progress into:</div>
                    <table style="width:100%; margin:0 auto; border-collapse:collapse; font-size:9px; table-layout:fixed;">
                        <tr>
                            <th style="border:1px solid #000; padding:6px; background:#7a1f1f; color:#fff;">Offense Level</th>
                            <th style="border:1px solid #000; padding:6px; background:#7a1f1f; color:#fff;">1st Offense</th>
                            <th style="border:1px solid #000; padding:6px; background:#7a1f1f; color:#fff;">2nd Offense</th>
                            <th style="border:1px solid #000; padding:6px; background:#7a1f1f; color:#fff;">3rd Offense</th>
                            <th style="border:1px solid #000; padding:6px; background:#7a1f1f; color:#fff;">4th Offense</th>
                            <th style="border:1px solid #000; padding:6px; background:#7a1f1f; color:#fff;">Cleansing Period</th>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000; padding:6px; font-weight:bold;">Minor Offenses</td>
                            <td style="border:1px solid #000; padding:6px;">Documented Verbal Warning</td>
                            <td style="border:1px solid #000; padding:6px;">Written Warning</td>
                            <td style="border:1px solid #000; padding:6px;">Final Warning</td>
                            <td style="border:1px solid #000; padding:6px;">Dismissal</td>
                            <td style="border:1px solid #000; padding:6px;">One (1) year</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000; padding:6px; font-weight:bold;">Less Grave Offenses</td>
                            <td style="border:1px solid #000; padding:6px;">First Written Warning</td>
                            <td style="border:1px solid #000; padding:6px;">Final Warning</td>
                            <td style="border:1px solid #000; padding:6px;">Dismissal</td>
                            <td style="border:1px solid #000; padding:6px;"></td>
                            <td style="border:1px solid #000; padding:6px;">Two (2) years</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000; padding:6px; font-weight:bold;">Grave Offenses</td>
                            <td style="border:1px solid #000; padding:6px;">Dismissal</td>
                            <td style="border:1px solid #000; padding:6px;"></td>
                            <td style="border:1px solid #000; padding:6px;"></td>
                            <td style="border:1px solid #000; padding:6px;"></td>
                            <td style="border:1px solid #000; padding:6px; background:#e0e0e0;">Not Applicable</td>
                        </tr>
                    </table>
                    <div style="font-size:9px; font-style:italic; margin-top:4px;">(Superior will encircle correspondingly)</div>
                </td>
            </tr>
        </table>

    </div>

</body>

</html>