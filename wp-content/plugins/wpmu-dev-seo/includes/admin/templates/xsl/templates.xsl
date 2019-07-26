<?xml version='1.0'?>
<xsl:stylesheet version="2.0"
                xmlns:html="http://www.w3.org/TR/REC-html40"
                xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template name="sitemapHead" match="/">
        <title>Sitemap</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style type="text/css">
            * {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            vertical-align: baseline;
            box-sizing: border-box;
            }

            body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333333;
            line-height: 20px;
            }

            table {
            border-collapse: collapse;
            margin-bottom: 1em;
            width: 100%;
            clear: both;
            position: relative;
            z-index: 2;
            line-height: 20px;
            }

            caption {
            text-align: left;
            margin-bottom: 30px;
            margin-top: 10px;
            }

            #content {
            width: 90%;
            margin: 0 auto;
            position: relative;
            }

            p {
            text-align: center;
            color: #333;
            font-size: 11px;
            }

            p a {
            color: #6655aa;
            font-weight: bold;
            }

            a {
            color: #17A8E3;
            text-decoration: none;
            }

            a:hover {
            text-decoration: underline;
            }

            td, th {
            text-align: left;
            font-size: 12px;
            padding: 10px 20px;
            white-space: nowrap;
            }

            td:first-child {
            white-space: inherit;
            }

            th {
            background-color: #F2F2F2;
            font-weight: bold;
            }

            tr.even td {
            background-color: #F8F8F8;
            }

            tr.even td:first-of-type,
            th:first-of-type {
            border-radius: 5px 0 0 5px;
            }

            tr.even td:last-of-type,
            th:last-of-type {
            border-radius: 0 5px 5px 0;
            }

            h1 {
            display: table;
            float: left;
            font-size: 16px;
            font-weight: bold;
            margin-top: 30px;
            }

            tbody tr {
            color: #666666;
            }

            .header {
            background:
            url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHQAAACgCAYAAADdNUgeAAAAAXNSR0IArs4c6QAAK+9JREFUeAHtfQeYXMWVbt2e7p6cpQnSKA4IJJLJRhipBQIRjNdgC4NIsk0yPCUElte7C9pd/GBBiOiAn/H6g+ewyDxYMMFCQiMQ0SBAWASlCZqc80xPh/v+c7ur5/bt2zf3TItvS9+o61Y4VbdOnVOnTp06l7H/CUwURYH+li9fnkF/FD9Sh+WI7bjVAV++/KZCr9u/JsPjPt+b6VngdnsK3G53hoAghymGw2IQIRAM9oeCY/XBYGh3KBR6KRj0v7hly5aQvGw6xeNeIp065nRfrr5u5U8y3Z6bsnNyZ7tcLsvvDaSKY/6xvr7e7s6unq6QGAy9kSFm/HLbG9s+crrPVuBZfjErjU1GnWuuue7B3Nz8W72ZmVlOtT825mdffv4ZseooSCHMBPa4IMzaUFPzu1Gn2rEC5yuL0Kuuuv6K3Lyc32RnZ+dbGRitOs3NjayzvT2xiMA+Bfe+cvv27Z8lZk5MylcOoTfddJNnxB/aWlhQsFi5Ljo1pJ/v/ZQFAoFk4EYEl2t1Tc3rv0lWIJXpXymEXn311QuycwreAlUWpWrQBgcH2KED+3XBC0x4Mjs367ZXXnnFr1vYwQIuB2FNKqgVK669vKCw9JNUIpNesLe3x9B7ikz84cjwyC6f7+IKQxUcKvSVQOiKFdf/qLikZAu2H26HxiUpmIG+/qR5ygzITKcxcWSbz3fpFGVeqp6PeIRCiv1fxSXFP3e5MlL+LiMjw1g7x0zhApLwcUwceO2SSy4pNlXRYuGUD4LFfhmqtmLFymsLCosftbOvNNRQtNDAwICZ4rGyoNSvDQ4OPevzbUw5BzliEXrFihVLiooKfpeRkTFhgt2QRYRKmBXZElHc+WAMyymKZKQIbkrBXnnllTOKCkve83i8npQ2pADe1HRYpkxQZBp7PHPOnOqDdXW1e4wVN1/qiKNQUpzn5hS+74Xqx/zrWq8xOjrCwiEHVLhi+HGf76Iq6z3RrnnEIfT66294Licvd0K3AjSEw8ND2iNpMBcTspCFR1OmdDiiEIrtydUFhQXfMjh2jhYbHXFORYs96jKfz3e1ox2MAjtiEPrtb68tyi/I/22q1Hl6gzuKLYuTQQyze3H2mu0kTIJ1xCC0sHjgZS+C0wNgFB6toQ6HGZ3tneschnlkIPS6677/D/l5BV93+uWNwiNhCAfcRosbLgfWe6fPtzzPcAUDBY8ICs3Lz/+vyWK1NIb+MXPaIQPjLhWBwqFIFDtvNFreSLm0R+iNN9+2PSPDPaFbFOXAjflTeGAiiuuc1CClNULXrFlTnuX1LlEO8EQ/a5x9OtGVGYKw8xInABGMtEao3x96hSmMt5x6cTNwYCpmprjpsmJYXGm6UpIKaYvQlSvXFkGz97Uk/Z7Q5MBYihEqskucOmJLW4RmZgaeYyDPCcVcksZ6e7qS5DiW7BHCg44oTNIWoR6vd5Fjw2UDUGdHO7YsQRsQjFUNC+I3jZXULpWWCP3BD29+EEvnpPdtEMdlH777NstIvSEErEDZ+dAc2VacTPqgqc23rKzMW9TSJzJtZHiYvftmDU5YwhPSLPakeR0dPWfYbSztEHrzzbdeiH2n4zpOMwPV093F3ti2lQ0PDTFXxsQdGQtiaKGZfqqVTblJhFqjWmkic9Gp/qQIQ6TiO7DvS7b/870MVx6kbgrWb01ovaZqHqj0q4XQdes2nBAMBearvm0KE8OhIKuvq2UHv/gi4dxzQmeWACtBmyGtKNQf8G/OsHGRyOxYkPRau38fO7T/S+ZPot7DgbRZsJbLo63pS5cuLdy2bVufVSBpg9C1a+84KxQOL7X6ImbrtbU0s092f8BGIfxohcyJtXRhYkBcgP68o9Unrby0EYpwHXOTVkedyiOK27vnY/berjd0kXnqaaezex9MuaFe3KuFBPGYuASTD2lBoavX3XGFGArbFgj03p1OTT7AvrKzvU2zqMfjYStvuJF96/LLpHKQuiEkpV65EO1UlWbndDInHaGrHn00UzxQf59OP21n9/Z0s7+9vYvR/lIrlFdUsH+8625WffRRsWIlJcWso6Mj9pzSSFg8shHqOlh/Z5iJc1I5SA21h9iejz7UNcM87Ywz2Pqf/IThQD2uO9OrqiYMofD2MC2ucZMPk7qGrlmzYWZYFP9R3ueBAeOXgeT11OLhcJjt+fAD9vEH7+si87Lly9ld99yTgEyCO71quhr4VKXZugo5qSxXZIHNGJUcPjI9PT2ssaGWHXf8SbY1NH7/KHt/15uMtD5agS6s3bZ2DVu67MKkxaqqZiTNczoD90oL7MCcNISuXn3nxaIY+g7vfH9/v4RM2vaFwiFbCB3Cpdz6ukNsCKo7rYC7pNJ6efJpp2oVY3OrqxkuRDGi+FQHSOG2EDopLHfjxo05TAj9nA8OnFAMNgABfA9v57iKjrsOHdwvWelNqazAiar6KxYWFmJLspnpIZP6mJOTzWbNmsm7m9JfnDLZOnFRf9uUdpmxnp7+u4C82dQMXmCorvagSz77rZxwUP2G+jrW3NQYmxhej5dl5SQ6PymdMoXdt/mhOElW75WPmU/7/dQHUKgtnNiqbOX1Vt1++8kiE9bzukODg4/jmkFsHaV0OXJ5Oa1fcjNDSnXamsjD0NAgGxlK3Kbcdc+/s6qZ5tbFBQsmRsVs9xx4QhEKVusWQsJvMQv52v3moQP72uVIoLiIewJGwwDW3v1ffsGUlu3+kRHW2dqiCua/fv8H1XStxJmzZrOJUQOKtgyYJhSh3b39G4BMbvgV8rg9q7AH/YZyIGFRrkxSfW5ra2W1hw7Ejrp4oYH+Ptba1MRgTcdys7zsNxuuYndcdT7PZm+/+Sb71WOPxZ6NRDJw43/m7FlGitoqgze3dediwhBKR2NMFO7ibwvW8sTmzf/xCdLO5mn8VxC0D5WD8BFE+thGrJk8EJseHhxkLY0NrKutTaLybK+b/WLdd5nvlPnslm+fw65ZdiYvzl564QX2s41347gskSXHCikis2amXjDCtsV4hxT9o0ftkVOpYCXpiSee8LS1d7wM6ozu0IWevFzv5dmu7KoQC/1ECXPKlKkMRmLKZOl5EIqHt3fWsLbmJjbQ18v6e/HX18d6uzoZbVdCUYOu4twstvnWS9mi046PwVl00tFsbx2ouqVTSms8fJht37qVFRQVspmQYuF4I1ZWHhnDVYjXt77GXnz+eebCvjWVAXaOB+rq6/7TahsTcn67as36fwfJ/DPvJKwAVj/68IOP+XznroTXy4TOzz/ueIbr9rx47NcbHGb//dKrLKBx18SFETl7QRW75ZtnsDNPPinB9mEsEGS3bv4Tq9m9LwaXIrSNWXjOOWzescewadOrJMGsubGJfbrnE/b+O+9IlAx/DmzG3Oq4eo4/COy5nTtrLrcKN7XTDb2KnnPG1HtgtXtKivJ/SR0GxZ6j7Dht4NWQSSrBH52awxaXLWa7Pmtk+5t7WEffMJQQYZad6WFTC3LY/Bml7LSjK1lZYQ4rLipgVCc3L09SCvB2vB43+/WdV7MH//Qae+K/d/Fk1gcqf+Uvf5H+YomKCJmlhGBFn+FOpWsHwdYpQEoRumrjxoJQ98DvMS4SLwMywxmujBsh7QZprAQmLoIQEBfUnGaSsVZ97UHWd/wJrKI4j3337GPj6qg9kELhwMGDDH73WF5uHiiwAH9FzAs/G2QUceeKC9jS0+eze5/+K9v9ZYMaCNU0uueSSoRCqNE+21Pt1Xii+qIxnm8r9vUTT30SdLgoBkRgjz3y8CakMbZs2bLKYCD0s1heNFJQUMgKwP54oDXx0MGIJDsGqXVBeS7P0vz1gGrLyyskKiUkwDRUQqzcP1VlaSFbvuQUtvCEapaT5cG1wRDrG4JzDLTDA1H/0VVlzHfyPPaDby5kR8+sZHsbbBERB63+63L9X3hJ2a2eqZ+aMgpds+bO68Ji6GreBUhvB70e4af82e8Pnsfj8t/s3DgdA2uC5od773pvfzu74pRpLGzg8hC2RKy0tASsF5MD6yqx8mThtGNnMvrjoX9olAXByrPAnnOw7ZGHtz8/LH9MQdxVZwdoShC6av36BWIw/AtZx0Jgc9du2rRpXFsuhlQRmps7ToHEapXany/aR9i8Eu1uE1udVhk5VrRiV1sACTlZOGpaSbIsR9JdrnCtHUDJp61FqKR4FwJsCwSeccwIwr0PP7wpZviEPDjtFy5QNkFXDrKysmPJbW2Jmp5n3qvVpDYyFznqqKOYG9SVilBWmMsKcjJTAZr02v7S0tI6O8AdR2h3T/8T0PSMa7IFoeacs8/cKO/kkiUX4Lwq8WSe1k8eRkdHGan1lKFveIz1BZJ0G6x17hxS0SWnMCU8K8+polIcWHxp9wMFSUbGymsytnr1+jXo1DXjtYXWLK/rqiuuuCI0noZYOPAPcc/RBzlCuzqTCx4vftSoVp1VVJSrWhyoFraReHRlatguhO+9NrolVXUModhv+rAPiZliQmPhz3AJlz/wwAOt8k4SuwVvuUqeRnESWuBUSkomKVO5dsrLf1TfxcLueGGF1s2K8vJYsfCQnw2//QULHI5YLAy/+Tnrf+YtNlabcBYQq2M0cvS01HhKDTP2kdE+JCvnyEJzxx13zBkbCz8DYT8GT3QJP5Svm7wD55577iIgtZo/89+ioiLgOTK/+vt7ExTuvBz//evf29hFx44PbEVFJQs29bCxQ23MD8UDIVMcxcGFO4NlnzaXjby7X6ra9+d3WdYpc1n26VhnpxQwV14WEzwZUOSHWRjsPNw9wIL4y114LMtAvlqYW5EaCnUz1wdq7ZlJiyHATCV52Q0bNhSOjAZeAjKn8nRs5n/66MObSKGQEHACcktCIhJKoL/lobszomvlz2q/r+9tZhcfX8HEoJ+RSq64uBhnNEOs70+7WKhzYLwK/AtxZPLE0d2HGP2pBe+cMla6/ltJkUl1Kory1KraTRO9Od4P7QKxhVBItO7unoFnsW7GTn+x49sMZN6r1rHzzjtvXjAQvgICUVx2DrYqOTkRoZjumJCjfiPhL5+2sUvmF0nIlPaZpfms+DofG3juU+atnoVrBUEW7MKhN4Ql77RKJpD1AiRpAVQrkPF0Nyj6IJB7sJ7BZoXlnHUMK7ntIiZ4MzSb93pcrDgvm/UM2jrpimsDXdyLDw4kSoFxpfQfLCOU1sLVa9c/CdyM7ycF9uijj2xan6zZUCD8b0BmwrpdXl4Zq9KtY6UXK4hIzWdN7MLjy1lBwThrzCgvYp4ZlSwLinlXJtZZjBS+ecVE7GlDkJpDXT1stO4wC9Q3Ss8SPEgjhVd9gxVcNn68Jm9HLV5WlOMoQnGMuFOtHbNplhG6du36zUDmdbxBnKDcjxOUDfxZ+Xvu4nMvDYnh7ynTiTrzowghYagHx2BmwtNv17EH5s+LVfFML2FD733ABt+IbXtjeWoRDxT6xbcsY5lQ6psJhRrKBzNweFkIkW/wuJ3fBGoxAmz16tv/BWO/lsqSwt0luNZoIfP888+fBmQ+oQa7vGLcUJxsaM1a/O1t7GZ/hgDEg4BD7YLL9SnNXVnMSm5dxiruv840MqmtbDdQ4FwQs3KzdjgBzjSFEjKxAoJ1StysF9xqBaTZV5J1hlyIdrR3voD8BBIgyTY/eu0ALJx16FwiStbG0zs+Zd+YP4PNLIsoJoh1uiuK2OBre1iwtYeJ/gBzQSfrnl7KSOjJOr2aZR6V0J1k4BPS23HPJeS3ZVgQBxOrwodYP5NvvONKaz+YQujqtXf8G8T7fyGQmJ/vZri8Vz/00H3q4iLK0Dq7xLfkKeAKmqH4QEJM5fQZscQ+fOCGrPeshAAk2fv/3zvskRuXQeKNUA4JOPTnZCAzl/r6enyMp5d5HPyqCDbmSQnCbP+1xbkotGeeeSajYtqMXwJDt2M20be97i4pzr/hvvvu0bxnsHPnm/cAqarbFLIK4JeCiDoP19dKxtFmX4CX7+wfZrgnw06uruBJjv6SFf7BAwdi1vgH2odYbZczVOpyu35cW1vb5ESHdSn0xz/+cf6uXe8+g7XyAuh4/ugSPD99+OH76vQaX7z43B+Bmv9JrRyp+Epl+86eri6YYVqjTjn8P76xl31tTjk7xQY7lcOjOE221tZW1goLQwiB4wEz26FwGF8z/BvG1xFwmgi9/fYNJ434g78ES9gtsIwFjz5y/5dGWl2yZMl3wiHxcbWyHpyCzJg5K5ZFbKw1if1srJDBCA3+z57ZxX512yVsKsxQ7AayCGyob2AjKt6sD7b02AUv1QcanwUy5VPFFtykCCVLvc8/P5CBk5JzEpTrGk36fEt9YjgELVHifpNm4YxZc+I8c5Eg5KS3y75hP9v4hxr20A0XMlIAWAk0yVqaW1h7J/S+iqEehoD1i5d3swO9IWy3IkKYlTZ4nQwh4xked+LXGTqP9mTpoqUnBIXQm6AU1TetnDadTS0rj/Wbrsjv+/KzOJOPWKbNyKLjZrK7rlpkGgpdaWxqbla1LKxt62WbnnuftfcOsamVlTBAyzcNP66CwPbDwm98Ex2Xae3BkFBkBDSU7tOx16wBMseVsrKKRUXFknmkLIk1NNQxvwNrpxwmj9d3wFYXpiRnHjOdJ2n+HoAVIbHrrOAg9piJRbd/Us8eBDL7wQEoFGDL5YYvBjsBwuXDsB9yRKHA+6HSdZ5l/PfSSy/N6e8bxF6TG1LH183JyQGrHV83KbcPlKB2gB1f097TC+/B9hYsc9Wlp5MCRBVYe98Q+89tn7BtH9dKAtCp0zJZWf740VwAvv6e3PoxI4TKg5qpqTxfPy6E3W7XU/rlzJVwBKH9/YO/w8idotY0eRSZNacaAzq+npElX2PTYbXijqe98P4+1gqkbfjOQlYoMx0hm94/QSp++cMDjPaxPHQNjOE4JfLUOziK/e17sAGGgl8W6F1se+gUxJfgYKpBBtaRqG2ELl68ZB2m9nK13pD962wgk5AqD3QFgZA6UeH9L5vY9Q89z84/aS6bDeX93+vb2Y5P61lQxdNm5xAQinCwtZfd/+y7rHtgJKGbXlL62wwYm5/bBKFaXZ0PqRZNTPT5zscXbAPvYLegOjHoCl5RcUlcRbJEoIu56RqOLc9jJ5R52SMv/o2NBcYpV97fQsgDxVNVRQV5MY24sK9m5+vHOrld4Y2pIoJnav1GdLQdTydDJkmzSmSSaq+xcWJYrVbftfI+r29lf32rGUwnealMhe1w8pLqOTiZeiAVyKTWLCO0vb1zIwSOY9W6nAuFe0XULpbn0wA11NXpupfh5Sfjtw+nPaS10gokXMlNTbXKJslryc7OfDpJnu3kcUnFBCif74JjMYXXqVVxw2J9Flgtvbg8tLbAzsehTzbK4ToV7wUi9ZBJbWXjnoyWFb5ef7BV2YyTFft6ziQNWaNQcewhwIuXdKINzJw9B1Ye8Vl0s6uj3b61XZJ3sJ1Md0x7DVpK5EUtE600Cn+BYn9vv+NbFXlfTFMoFAgLwT4vlAPh8SlTy1geru/JA2mDGhvq5ElpFafPSHbDFY6RQLfOuO2TkfLKMr3d3UJfZ/dSZbqTz6YRCpcz/6rWAbJWr5w2bn1AZUgnWld7SNckUw3eRKV1meAchbAstBrIjcAgOJXAyEgudcEUQnGKchyoU3WGkfs0ufKAutx4uCHBO0nqXsU8ZD+uW2jdBpdDpGUk3wa77cFNADoNgmnAxfOmzZsih+1k3BRCQXC3qjVO19lJspUH8uilZf0uLztZ8SETjiKLy6YmTFij/R6GvyT4Y5KKA6meMTb8A6N1zZYzjFCfb2UWpti1ygZImq2YVhWXTA6fWpob49LS7YG+ADGochlKrZ85kAtyId1aCdSOnK3jbBm7PXEVbshlWoGnV8cwQgWhfhmAxZMhEkpKp8Q5ZKLb0vW1pOjWa3py8/u6u6U1Xq8X5K1ziuzIT6+8Mr8TLna4mpMmPz7QR0WqgsOjktWksrzdZ8MIxRWG7yobo61mmeyCECGRnDA6eWCtbNOJ5zEc2fVhq6IXCAFlldMtewalfS2xWx74HMeXL5AkbqyeNm0hz3Pq1xBCwffpgu7FykYLoNOUHyO1QHmg59JUCWOin0ny7pBMXvjwqveAkDkVUrsXvhmshH4cD5LmKS5gxpNgRHdx0HpWSGSvzpo+/ay4MjYfDCEU3xI5ATMqXsuOhqeA3fLQD3dsnSa2ALzexP7C9rcV1giByIlKsrYlZMIA3Oqek0xSu5Pcb6UJRcGN+zVAaj6siV45atos1aPHZP3TSjeE0GBQXKwE4oWnL26CQetmOisPeN87YLmn5p2T59OvhEyYl+TkjXsUkOdrxYn6SEnRo/HBARKSKOC2Ac5UgVSY6wTFwKtzq6qO1oJtNM8QQl1MTGAL8pOUxsM4W5QdEhttfCLLdcAUc6hf+1ZbZM0EZVqQaInqG+BLqR2TJgAvLYRctUCp5LGbAnw2YX3G6OIqZigkvgrJ186ZnATTEELhMfN4qbTsP+5LiG6LpdqURNas6Shd5G3HV5T09pykFCnDmpmNy1NmArFQ+g7Mof37Y58QIY9jfhwVEudSC5TP0e3GtcaIsl+cGxgeed7udkYXoT7fRjeuuh0j7xiZX9D6QlTZAj9C6Rpo4NrgZpW8dGoFiTIJmdE7qlpleV4YVNaNm3L03TRCKExXeVbslyhxDJSbQK3AJt/KUGHaGklIFcWFweGRX8UAWIi49eoIws5qyGZxNhf5UQU8fT+MBi0dA30VogP9C+LSr1YgP0blMC/NzMrSKiblkWPm4UHcEocX0AHoZY04aiYKJqRmkjNK2udFA6WTK3bu2YyQSkiG78KVs6dPf6+uqckSYnUR6gq7ZoZZPNJyYI9KetCuLkcuTPF3dOx3EBJ3VzvpTiMSZTLAZIZZNn06DLLj5musOLFMmhjkHZu7O9eDGassixCFBoAspW1VEJ/f8gge+COMIJo4n3Q5WRQfgeT7/oHm+t0yMIaiuggNi+FKJSQyy6TFP90CDRyp2QiheoH88VYAmUpHjMRx2rG16e/rV2WjenCT5RP7zQhDCFK4qAtISIWbgCj1ElJDgaAXzgT+eOKJJ56yZ8+eoWQw1dJ111Cw2ziEUsMulxtX6uJNG9WAT2QaUVJzQ308MjHxaQBpI0+slfSoFLLwvZZKnA4pkTkM3w61WBPJZlhtTbT7PnSZmVgtF4gkeJiE0iXnqFRMPST2i33/vP6OrgfNtqlLoQAYdz+PNsR9QGa0fbPtpaQ8IYA8WnPhg/zDw9RD2uvJGxSR5oXruSlQV3KKiOTT/rFDEm7oSmKqAridtJ4SfIkwMMnc2LpQIKTGLPGBVZJ+waZvnjtt2p8PNTdvkwoZ+E+fQoX4TzdRo+lyLEaHxuRjPnbWSAOB223k74827spAqsqp+PqgHJkkrbbCTlhS1isrpPCZJh8JQbS9obWUJpLcHYE0IYFwqAd/fVZV1bgDRJ0+Jb51YoW4cyOSM5J93jixaupS6OiLWKx/ZFRqhFgr6ZXVEAkMsiJ8fCcf91HkgSZEA06GeiG1+kkdmELqlLerjBMiycSVvHNzpQOVoaUCq8Sc1pCoes9WCYeedRGK07s4EVA+i9QApjqNKKqjpYV1Qiij9YgCsSdadyIrZHwPaO2cAkfISu0PPv7D6g7uZ2RTlA6BKDRASMXenrzBUKD3IW0SLlrfecws3MM0EHQRCmhxJnyTeTRG24fmhgbp6w/0bsQ6aSvA93LK9yVJtgx6Wa/iO9qExIa6g3EsTll3Mp4JqbRnJRbMuQVNSKDX6w8E/7eRPukjFEKhEUCpLtOPE4xWaKWITVIgJBIy5euhvA+kwpsC/38uUK88jEjIrE1bg2/iOkQ0tG/FDiNCpaTvFcXvGTmV0UUoZofhBVk+cE7FSXigfSFJoVyKJfZKbFYtEIILS+CaHD4clMiWTErr0heZ/H1oWaP1NByMLCnEdullgmJQdy3VRSgTxElDKL1YC1js8EBEF0sIklgsBCC1QEJEKdbL3PxxV3G8HGl9yJoiXVWVvJ/8lzgRCUhcTiArB1DsZUfNnLmAl1H7VR8ZeUmRjZ9iy9NTHA/4xyRkct9FdDXRQ4IPkKoWvNDFTsV9GuV6SWVpUJpg7M3ZtVr9dEujPavEfklXDi4lma0QlQZCq7T6qotQwCjTApCKvDFofWh/iasDEnhiOdL1iiTIzMf3WEiSJQFCLbTCAnEUAtWRFkgtSMgMglIxk6H1kiwGr8ERWyILir6cJkJ9vm8XYd2yZlRjcfSIIlsON8ZYjRtIopN9tcBZrHJ/KS9LR1x0d+VIDJJTZqJUuphMVBpRluSFhka/l+x9NBHK2NDsZBVTkR5ZMw+j71FhAIhMRnWkjyUWq3XsRbpZOtw+kgM/NyUhibRHtOCEBfHKZO+kiVAhHJybrGIq0lvgR0+OTEm6UzYE1lMAKbYEtrLJkE1VaL1sgkovXYOqRkuls4RIku5jygYSCEXRV11drboUaiIUUpUjhksq/UxIam2E3wVaKxAkNhtVWssLkqKAqDJPRYqVl6N4SxPgRddgZd5kPxOVKY/RtPpE2jFpogOx0bNTV9jvv0CtjiZCQd8JtkRqQOym0RrHhRZCphrl5eP+DCkKlIfEkbZFWPMNMtpnUiAnyvwuSSQ/vf6n90si36l2NBR17gE/UDFdNQzfz1MrrL47j5UUyB439pSKSBhUREoDCjRrlcgkBBbC/ldtO4JpKyFuCAfatL+smDFDsqRoh4VfOgc6DeJKEiP9pC0MUagYxhoKx8u0dUP9xWp1k1KoD5eTUElzE6sG0Gxae9SKnToZ7/tHYHmgSrW9Je3PyCqhDapAsk4nYSoTVhSkzm4G6+brsNm+TER52k/SR2vNUCj1S1pLaT1FPLr+zlHbviSl0Azx8OnYBcUp5p1+4WBwjI0OR/aH8tMSWiuLQJX0Kw90l5PMMekjd8CaPAtWCDmsC0bOfhXPmXEFJ/mBqJMCt54w2h3iQKTuJEsKaR3FRgDfmTkR9XfJYSRFaEgMOX6RRt4wxbvaO6UkUrQThdIf7SnzpI8KRDRCRI2jcHNKWxC+RirhSJ69AIMQms6BKIsLQ/SuZgKxaPqj4zUxWjcshKoBwxhCMYdgLR9PBWY6YKTsaNQrCs1a2lcWlpTG2C5RGplMUhl6Ea3gwYWiVnxkXa+cFoyJyFMKdIRUM30mKhWg/kQlqbtYVquU/U5KoZgPKaXQkSiiiH3QV5XoQ7DkmZPWRqJIo0p0ejU6GB5BnXQOtHYqqZJoVHuqxr8RbetwzhRLBPslSfdnsQRExnNlqZEvIIVs37OQgYxGBdJhSYLYcH/kBMWFRzpwJuGG2KvZQFLyENyopnOg9TJmACbrqOSTQoy3eZZlJ0SJmukQnIQqChCREm4Eqkq5OIdbmgDNZgJmZwiNfR+sXLrLx09RcPMUe8ghS8ikLo2NBSzXtflKhquTpaSaEMQNrA0DQkFSMvAAtEY23jwBv6oIxSRQ1ULI6lmIiq/teGPHU6gobRI5SyWq5Boi00DRUclcw3TFiatAQhCXbJWtqiFZWUb5LCnqeaLIEizKExDqw+UkrJ/n8jpO/WLswcnPK4W//ARtOUludABNyCHkQguSICwgRfpHm2yaDNI1BWxjzAgVTr2LGTgRo2n1GpYoFO/PA0YqQaxXWUN3fR0V8nklB389uAN5KbSRQCjWAfpgTuS4U2qCECPCCJVCSHGXRkpU+S+dFQjUXTpc0FLCR85OVF5MJ4kmNcHF8VoCQhMoFIYs5O0kNUEMnwdnDRKFejz2j1nTmjohuKgJQnEDS8JNVMCJS9d5oGWK/oBQGUlEKiUgFBR0sQ481WwSelQzZImgvzPx2EhJEeWBLPMrFvXA8xjQpRuMlFECoSWJlh0oGLzKvDiELlu2DBeTxJOVhQw8k7D6A7zB55plRaEaYpgkFJGZJdeaaNbRyIwYOWoUmKQsSRCCMGQkWBkDkibo5AUCRI6yjbhWx0bGLkIBU5Mmsh0RVpIEC6Q+q2wg/hlf5haE2D11O77zCC5gxYNPgyfqE1Gn8WD+Hcg0RdIWCUKCRWYcQsMCu8R4R6SSAVDJVdHtCD6DLDyvVx92QLG9U/GUMltUGr0nq9fkhOaTAt3MRONKAjOd5NolTIXkCL3ppps8wPr5RgFjIo7CaOk7O3e+voXXWbx48UeI9/BntV8s5tDRkcYoEug6vNVAmhYzg2e1HaP1CDnSBSOjFVDOCkJj4LVY7r59h85GQUPbFfS7F7Z4F7y+8/UXY8AR2bhxIyyZhDflacq4EBKC8r1oJpTyxaXWtYyqdkfKRifoWenJ20izZPhlOWiz3PCFRgCDIprwGflztu/croo45P9NB84oxLN6eZnCkmKWK/swujxPL06H4uZXIT2o5vOJMq0IOFJLoBCLIeEbc+PTQwzr7j+BrL3eTM9ZNTVb/56sA0JY3J0sT0p3szZcr6hVlpkKQ+nsvFxlsu4zDYUVytAFbLJAsrs2RsBYZruimOBiW0Koz3dxBRQ1J2k2LrDXsnOyFm7duvWwVjnRlfmZRr4YCpU0Q5A6pFamHJ4vQam05vN1X61YQhpRRzJ9aULhFCRQ+3bWcqt1sQ9VP20RxVEShpLSPWbQ/xEE38X4PEW/3nj4fF9vIIFJrRzS62tqtuDcTDiglo80sbyi8hYoze5FPCY4JSkbl0z3XsxtF+Kq23qwO5msIhRCbD4+iJQh73yU5Yo+eeJ4XAhj0b5zx84dN9XUbExQM42XG4+RYATyUqdiUfiESrpc4r7xGjwmkE3bDWjr1/Uth/8JitDv4jRCdWLwGvJfySoObCbprJQXdjAurZ3W10CpJ1r6Xp2uCm+99VYc25UQikFYrKyIPg4i87Kamtc3KfN0n0VBHaEuoYbqYhC+UMCgU+8bsZ/9LU+vb2x8bkwQ54FoVdkz6XHJ2m8MpzTkeMKPkxc6rTHFq3ljNn6dkLItUyj67QoE4hF60UUXTcXgVCve6TCOdr6BAX5BkW7oERv+NrWC2K68Run4zGIffmJIx3byR3Jk8rrNzc2H61taqoGlxzDpYq40SfUV8x5Cx21A7qQEzHrLkq2sw3YQinV0igwUc42Ojp4hT8D69r7gyjljx44dEnuMzzP8FDHniysu7APMvTwJ7FSShoml19TUPMHT1X7rW5tXg5fKtkPODKRaW2bSrB5/qbVhle1CDRi3iXfjDPJrvAEM8rNTy6dcu2XLloixLM8w/5twfw/2bU/JwWByvw4D0wNGWXpYFPIBQwJB6yT555NOHECd9I9O/2kp49oj8llLR0ypDNKlXJx6KK39rbQpUakFRgMD7HiEAhA+roMBcbke2bFj+zo8WwCb8AoRC7BoMgY6iI+oxiE0KyfriZdffnnMKLtBt/Kj+Iw1RgJJsjBRbHgM63YmbVuSdcRgumS9YGX+CUIcQl3A3jx0ZiMoZa1DyKRXiJNOQTt/VO5f6Qt9ZtrDnMszODbSmjpRCEVjuj7sjfRbsgA0UlBZRnHDnrTbf655o+ZfleXsPEOhGzsiAxzR5XbdZwce1cWkSDj7SwYz1axW2S61Z9dYzYa2qFzeH9fUqaUPyBOcjgMRT23fvl1Le2SwSeOuAWhtm+jA13Or7dLSY3T5UbRRJn92QwDSNR2RVzASF6AjACdCEPozPK4NRurolhGZ1+hCxb+8oAvTZgHpJhnWT5JQLSIjrgdEpaHIwMWlaz6IYjxCNQtbzMRHe2DrQoKWcDeoU3VPaho0jhqN1KG10wmpLllbknkJDrElNzPJCllNx8QwqfGku0BV8uYIguMBQLPBQN7y+RY96gRwn89H5qaGBMlUrZ90mpKJ641Z+E6q5D/eiRdTwKBtl9kAI4NiTOJYzZQgFA1gc5Z5jXTgbbaHKuUP7907G8mxTqsUiSU5vX4SJWZ6M+EKwMu8+JXusabI9sWscoHYPDiGMKOsLKbpSwlCvVnex2tqXq2LjbLNSNjjmWsUhFPrJw0WfT2KEElI9MCxMqURSyfHiqkIZi3puR5ZzMg4hvdHxXKeZ1n/xZ5zyHrtxJpwLT4fdyQSMxQpTq2f5LiDey4jZPItRWR7AhnSrOCi6GfSRzqnMhG4YgWsaw6vlhKEcuCO/YbE+UZg2V0/iadLXrHBZon9SfdSooyetiX0l8pgZrmQS9ZgszN5v44MhNIxmoHJa/kWG0aDqJB8OkCYk05QJGRGR4n813LHT3zgnP41y8o5dVI/cGc0Zjp5RCAUHA7novqBLsNaCS5cKvKCtZLcRQPFB4sGmb7MlNqNUKTHdJhgJsiP7UChsb3oEYFQSCNleuuWxK4sIFRCJnwhUSAnkTFBA+uZ5A3TCGswgwmVsvRlQzPLhZzdEriwIJRysCmRcjlwJ35nzZo1H8jUvVsQdxHWYMM0y71RZBKL5ciUhB+iGAsTxGDTsWLwSdhLPojMBM5BeB0s80U8nvYIdYVC3+ed1fo1I1AQHEImCUAUSKLlLIzcsJFpizXmLYEz9h8mCz4tEsalo98bqzBeKuH8VXYSlfYIxQDrXm+kNc4My6L9JL/uJ21LoooCEqomwuEj9ZVMaGBt8CSEsa3jqNKPEXVGBe9YYbxP7I5L2q+hOG6fl/AGsVeJRKTbWIq0pI9AJlk7EFIjyIzM6YnYllCfqB1JABKEfnTgn7H+5cDzR9LuKjNUdciye6JpjdBZVbMuxqf69NdPExtykmYnBZlgsaRh4lsr9OGe1ra2dkJYeUkJGc0lXGtQIpP6TUKcSojhMb1ZbiiwTqXzCUlG188IRUILREdeWEMpTARl0vbHH/3KQ7Tzh0rLyx+JxokB7eFxrV+lMMTLoj48HUQU9OmNUCaczTut9Wtk/SRWRScmNChcqCDpMtXaH4JPPpnkDj6wbt69d+/ecT4rRAzQtd6R8rgUrlJOqCotlZQLaYvQ2ZWVuA2n/80YuSMmlReVkohVcZUen+WkhOD+3JPVs5VOLBb7S1ov5RIzqOnvN69a9Qc5bFDXx/JntTi9A/0lDZ5s6Vz0/wNWrF+4qBeoMAAAAABJRU5ErkJggg==")
            no-repeat right center;
            background-size: 60px 80px;
            display: table;
            float: right;
            height: 80px;
            position: absolute;
            right: 15px;
            top: 30px;
            width: 190px;
            z-index: 3;
            }

            .header span {
            display: table-cell;
            padding-bottom: 10px;
            vertical-align: bottom;
            color: #888888;
            font-size: 10px;
            }

            .header span a {
            color: #333333;
            }

            .footer,
            .footer a,
            .footer a:hover,
            .footer a:active,
            .footer a:focus {
            color: #888888;
            font-size: 10px;
            margin-top: 30px;
            }

            .footer a {
            font-weight: bold;
            }

            @media all and (max-width: 700px){
            td, th {
            padding: 10px;
            }
            }
        </style>
    </xsl:template>

    <xsl:template name="sitemapBody" match="/">
        <div id="content">
            <div class="header">
                <span>Powered by
                    <a target="_blank" href="https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/">SmartCrawl
                    </a>
                </span>
            </div>
            <h1>Sitemap</h1>
            <table id="sitemap">
                <caption>This XML sitemap index file contains
                    <strong>
                        <xsl:value-of select="count(sitemap:urlset/sitemap:url)"/>
                    </strong>
                    URLs.
                </caption>
                <thead>
                    <tr>
                        <th width="75%" valign="bottom">URL</th>
                        <th width="5%" valign="bottom">Priority</th>
                        <th width="5%" valign="bottom">Frequency</th>
                        <th width="5%" valign="bottom">Images</th>
                        <th width="10%" valign="bottom">Last Modified</th>
                    </tr>
                </thead>
                <tbody>
                    <xsl:for-each select="sitemap:urlset/sitemap:url">
                        <xsl:variable name="css-class">
                            <xsl:choose>
                                <xsl:when test="position() mod 2 = 0">even</xsl:when>
                                <xsl:otherwise>odd</xsl:otherwise>
                            </xsl:choose>
                        </xsl:variable>
                        <tr class="{$css-class}">
                            <td>
                                <xsl:variable name="item_url">
                                    <xsl:value-of select="sitemap:loc"/>
                                </xsl:variable>
                                <a href="{$item_url}">
                                    <xsl:value-of select="sitemap:loc"/>
                                </a>
                            </td>
                            <td>
                                <xsl:value-of select="concat(sitemap:priority*100,'%')"/>
                            </td>
                            <td>
                                <xsl:value-of select="sitemap:changefreq"/>
                            </td>
                            <td>
                                <xsl:value-of select="count(image:image)"/>
                            </td>
                            <td>
                                <xsl:value-of
                                        select="concat(substring(sitemap:lastmod,0,11),concat(' ', substring(sitemap:lastmod,12,5)))"/>
                            </td>
                        </tr>
                    </xsl:for-each>
                </tbody>
            </table>
            <p class="footer">
                This is an XML Sitemap, meant for consumption by search engines. For more info visit <a
                    href="http://sitemaps.org">sitemaps.org</a>.
            </p>
        </div>
    </xsl:template>
</xsl:stylesheet>
